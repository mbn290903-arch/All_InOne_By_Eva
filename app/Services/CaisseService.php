<?php
namespace App\Services;
use App\Models\Paiement;
use App\Models\RendezVous;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CaisseService
{
    public function encaisser(array $data, User $user): Paiement
    {
        return DB::transaction(function () use ($data, $user) {
            $montant = (int) $data['montant_paye'];
            $montantTotal = $montant;
            $reste = 0;

            if (! empty($data['rendez_vous_id'])) {
                // On verrouille le rendez-vous pour éviter deux encaissements simultanés
                $rdv = RendezVous::lockForUpdate()->findOrFail($data['rendez_vous_id']);

                if ($rdv->client_id !== (int) $data['client_id']) {
                    throw ValidationException::withMessages([
                        'client_id' => "Ce rendez-vous n'appartient pas à ce client.",
                    ]);
                }

                $resteActuel = $rdv->resteAPayer();

                if ($resteActuel === 0) {
                    throw ValidationException::withMessages([
                        'montant_paye' => 'Ce rendez-vous est déjà soldé.',
                    ]);
                }

                if ($montant > $resteActuel) {
                    throw ValidationException::withMessages([
                        'montant_paye' => "Le montant dépasse le reste à payer ({$resteActuel} FCFA).",
                    ]);
                }

                $montantTotal = $rdv->prix_prevu;
                $reste = $resteActuel - $montant;
            }

            $paiement = Paiement::create([
                'client_id' => $data['client_id'],
                'rendez_vous_id' => $data['rendez_vous_id'] ?? null,
                'mode_paiement_id' => $data['mode_paiement_id'],
                'montant_total' => $montantTotal,
                'montant_paye' => $montant,
                'reste_a_payer' => $reste,
                'paye_le' => now(),
                'reference' => $data['reference'] ?? null,
                'enregistre_par' => $user->id,
            ]);

            $this->journaliser($user, 'paiement.cree', $paiement, null, $paiement->toArray());

            return $paiement;
        });
    }

    public function annuler(Paiement $paiement, string $motif, User $user): Paiement
    {
        return DB::transaction(function () use ($paiement, $motif, $user) {
            $paiement = Paiement::lockForUpdate()->findOrFail($paiement->id);

            if ($paiement->estAnnule()) {
                throw ValidationException::withMessages([
                    'motif_annulation' => 'Ce paiement est déjà annulé.',
                ]);
            }

            $avant = $paiement->toArray();

            $paiement->update([
                'annule_le' => now(),
                'annule_par' => $user->id,
                'motif_annulation' => $motif,
            ]);

            $this->journaliser($user, 'paiement.annule', $paiement, $avant, $paiement->fresh()->toArray());

            return $paiement;
        });
    }

    private function journaliser(User $user, string $action, Paiement $paiement, ?array $avant, ?array $apres): void
    {
        DB::table('historique_actions')->insert([
            'user_id' => $user->id,
            'action' => $action,
            'auditable_type' => Paiement::class,
            'auditable_id' => $paiement->id,
            'anciennes_valeurs' => $avant ? json_encode($avant) : null,
            'nouvelles_valeurs' => $apres ? json_encode($apres) : null,
            'ip' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}