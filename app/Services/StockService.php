<?php
namespace App\Services;

use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class StockService
{
    /**
     * Seul point d'entrée pour modifier une quantité en stock.
     * $extra accepte : prix_achat, fournisseur (approvisionnement), rendez_vous_id.
     */
    public function enregistrer(
        Produit $produit,
        string $type,
        float $quantite,
        string $motif,
        User $user,
        array $extra = []
    ): MouvementStock {
        if (! in_array($type, ['entree', 'sortie'], true)) {
            throw new \InvalidArgumentException('Type de mouvement invalide.');
        }
        if ($quantite <= 0) {
            throw ValidationException::withMessages(['quantite' => 'La quantité doit être supérieure à 0.']);
        }

        return DB::transaction(function () use ($produit, $type, $quantite, $motif, $user, $extra) {
            $p = Produit::whereKey($produit->id)->lockForUpdate()->firstOrFail();

            $avant = (float) $p->quantite;
            $apres = round($type === 'entree' ? $avant + $quantite : $avant - $quantite, 2);

            if ($apres < 0) {
                throw ValidationException::withMessages([
                    'quantite' => "Stock insuffisant : il reste {$avant} {$p->unite} de « {$p->nom} ».",
                ]);
            }

            $p->quantite = $apres;

            if ($type === 'entree' && ! empty($extra['approvisionnement'])) {
                $p->date_entree = now()->toDateString();
                if (isset($extra['prix_achat']) && $extra['prix_achat'] !== '') {
                    $p->prix_achat = (int) $extra['prix_achat'];
                }
                if (! empty($extra['fournisseur'])) {
                    $p->fournisseur = $extra['fournisseur'];
                }
            }
            $p->save();

            return MouvementStock::create([
                'produit_id'     => $p->id,
                'type'           => $type,
                'quantite'       => $quantite,
                'stock_apres'    => $apres,
                'motif'          => $motif,
                'rendez_vous_id' => $extra['rendez_vous_id'] ?? null,
                'enregistre_par' => $user->id,
            ]);
        });
    }

    /** Option activable dans parametres_salon (cle = stock_consommation_prestations, valeur = 1). */
    public function consommationActive(): bool
    {
        $valeur = DB::table('parametres_salon')->where('cle', 'stock_consommation_prestations')->value('valeur');

        return $valeur !== null ? in_array($valeur, ['1', 'true', 'oui'], true)
                                : (bool) config('stock.consommation_prestations');
    }

    /**
     * Déduit les produits liés aux services d'un rendez-vous (table service_produit).
     * Ne fait RIEN si l'option est désactivée. N'agit qu'une fois par rendez-vous.
     * À appeler quand le rendez-vous passe à « terminé » ou quand le paiement est enregistré.
     */
    public function consommerPourRendezVous(int $rendezVousId, User $user): void
    {
        if (! $this->consommationActive()) {
            return;
        }
        if (MouvementStock::where('rendez_vous_id', $rendezVousId)->exists()) {
            return; // déjà déduit
        }

        // Services directs + services contenus dans les forfaits
        $lignes = DB::table('rendez_vous_service')->where('rendez_vous_id', $rendezVousId)->get();
        $serviceIds = $lignes->pluck('service_id')->filter()->all();
        $forfaitIds = $lignes->pluck('forfait_id')->filter()->all();
        if ($forfaitIds) {
            $serviceIds = array_merge(
                $serviceIds,
                DB::table('forfait_service')->whereIn('forfait_id', $forfaitIds)->pluck('service_id')->all()
            );
        }
        if (! $serviceIds) {
            return;
        }

        // Quantité totale à déduire par produit
        // Un même service présent plusieurs fois compte plusieurs fois
        $occurrences = array_count_values($serviceIds);
        $besoins = DB::table('service_produit')->whereIn('service_id', array_keys($occurrences))->get()
            ->groupBy('produit_id')
            ->map(fn ($rows) => $rows->sum(fn ($r) => $r->quantite * $occurrences[$r->service_id]));

        foreach ($besoins as $produitId => $total) {
            $produit = Produit::find($produitId);
            if (! $produit) {
                continue;
            }
            try {
                $this->enregistrer($produit, 'sortie', (float) $total, 'Consommation prestation', $user, [
                    'rendez_vous_id' => $rendezVousId,
                ]);
            } catch (ValidationException $e) {
                // On ne bloque pas le rendez-vous si le stock est insuffisant, mais on le trace.
                Log::warning("Stock insuffisant pour {$produit->nom} (rendez-vous #{$rendezVousId})");
            }
        }
    }
}