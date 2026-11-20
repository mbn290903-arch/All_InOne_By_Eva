<?php

namespace App\Services;

use App\Enums\StatutRendezVous;
use App\Enums\TypeMouvementPoints;
use App\Exceptions\RegleMetierException;
use App\Models\Client;
use App\Models\MouvementPoints;
use App\Models\Paiement;
use App\Models\ParametreSalon;
use App\Models\Recompense;
use App\Models\RegleFidelite;
use App\Models\RendezVous;
use App\Models\User;
use App\Models\UtilisationRecompense;
use Illuminate\Support\Facades\DB;

class FideliteService
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function pointsPour(int $montant): int
    {
        $tranche = max(1, (int) ParametreSalon::get('fidelite_fcfa_par_point', 1000));

        return intdiv($montant, $tranche);
    }


    /**
     * Crédite les points d'un rendez-vous terminé et entièrement réglé.
     */
    public function crediterSiEligible(RendezVous $rendezVous): ?MouvementPoints
    {
        return DB::transaction(function () use ($rendezVous) {
            $rendezVous = RendezVous::query()
                ->lockForUpdate()
                ->findOrFail($rendezVous->id);

            if (
                $rendezVous->statut !== StatutRendezVous::Termine
                || ! $rendezVous->estSolde()
            ) {
                return null;
            }

            $paiements = $rendezVous->paiements()
                ->valides()
                ->orderBy('paye_le')
                ->orderBy('id')
                ->get();

            if ($paiements->isEmpty()) {
                return null;
            }

            // Récupérer tous les paiements du rendez-vous,
            // y compris ceux qui ont été annulés.
            $paiementIds = Paiement::query()
                ->where('rendez_vous_id', $rendezVous->id)
                ->pluck('id');

            // Vérifier s'il existe déjà un gain non annulé.
            $gainsExistants = MouvementPoints::query()
                ->where('type', TypeMouvementPoints::Gain)
                ->where('client_id', $rendezVous->client_id)
                ->whereIn('paiement_id', $paiementIds)
                ->get();

            foreach ($gainsExistants as $gain) {
                $dejaAnnule = MouvementPoints::query()
                    ->where('mouvement_origine_id', $gain->id)
                    ->exists();

                if (! $dejaAnnule) {
                    return null;
                }
            }

            $dernierPaiement = $paiements->last();
            $montant = (int) $paiements->sum('montant_paye');
            $datePaiement = $dernierPaiement->paye_le->toDateString();

            $regle = RegleFidelite::query()
                ->where('is_active', true)
                ->whereDate('date_debut', '<=', $datePaiement)
                ->where(function ($query) use ($datePaiement) {
                    $query->whereNull('date_fin')
                        ->orWhereDate('date_fin', '>=', $datePaiement);
                })
                ->orderByDesc('date_debut')
                ->orderByDesc('id')
                ->first();

            $tranche = $regle
                ? (int) $regle->montant_par_tranche
                : max(1, (int) ParametreSalon::get('fidelite_fcfa_par_point', 1000));

            $pointsParTranche = $regle
                ? (int) $regle->points_par_tranche
                : 1;

            $points = intdiv($montant, $tranche) * $pointsParTranche;

            if ($points <= 0) {
                return null;
            }

            return MouvementPoints::create([
                'client_id' => $rendezVous->client_id,
                'paiement_id' => $dernierPaiement->id,
                'regle_fidelite_id' => $regle?->id,
                'montant_reference' => $montant,
                'montant_par_tranche_snapshot' => $tranche,
                'points_par_tranche_snapshot' => $pointsParTranche,
                'type' => TypeMouvementPoints::Gain,
                'points' => $points,
                'motif' => "Rendez-vous du {$rendezVous->debut->format('d/m/Y')}",
            ]);
        });
    }

    /**
     * Annule les gains liés au rendez-vous d'un paiement annulé.
     * Les points ne sont recrédités que si le rendez-vous
     * reste terminé et entièrement payé.
     */
    public function annulerGainDuPaiement(int $paiementId, User $auteur): void
    {
        DB::transaction(function () use ($paiementId, $auteur) {
            $paiement = Paiement::query()
                ->lockForUpdate()
                ->find($paiementId);

            if (! $paiement || ! $paiement->rendez_vous_id) {
                return;
            }

            $rendezVous = RendezVous::query()
                ->lockForUpdate()
                ->find($paiement->rendez_vous_id);

            if (! $rendezVous) {
                return;
            }

            // Tous les paiements du rendez-vous, même annulés.
            $paiementIds = Paiement::query()
                ->where('rendez_vous_id', $rendezVous->id)
                ->pluck('id');

            $gains = MouvementPoints::query()
                ->where('type', TypeMouvementPoints::Gain)
                ->whereIn('paiement_id', $paiementIds)
                ->lockForUpdate()
                ->get();

            foreach ($gains as $gain) {
                $annulationExistante = MouvementPoints::query()
                    ->where('mouvement_origine_id', $gain->id)
                    ->exists();

                if ($annulationExistante) {
                    continue;
                }

                MouvementPoints::create([
                    'client_id' => $gain->client_id,
                    'mouvement_origine_id' => $gain->id,
                    'type' => TypeMouvementPoints::Correction,
                    'points' => -abs($gain->points),
                    'motif' => "Annulation du gain lié au paiement #{$paiementId}",
                    'enregistre_par' => $auteur->id,
                ]);

                $this->audit->log(
                    'fidelite.gain.annule',
                    $gain,
                    nouvelles: [
                        'paiement_id' => $paiementId,
                        'points_annules' => abs($gain->points),
                    ]
                );
            }

            // Cette méthode ne recrédite des points que si
            // le rendez-vous est terminé et encore intégralement payé.
            $this->crediterSiEligible($rendezVous);
        });
    }



    /**
     * Utilise une récompense.
     */
    public function utiliserRecompense(
        Client $client,
        Recompense $recompense,
        User $auteur
    ): UtilisationRecompense {
        return DB::transaction(function () use ($client, $recompense, $auteur) {
            $client = Client::query()
                ->lockForUpdate()
                ->findOrFail($client->id);

            if (! $recompense->is_active) {
                throw new RegleMetierException(
                    'Cette récompense n\'est plus disponible.'
                );
            }

            if ($client->soldePoints() < $recompense->seuil_points) {
                throw new RegleMetierException(
                    'Solde de points insuffisant pour cette récompense.'
                );
            }

            $mouvement = MouvementPoints::create([
                'client_id' => $client->id,
                'type' => TypeMouvementPoints::Utilisation,
                'points' => -$recompense->seuil_points,
                'motif' => "Récompense : {$recompense->libelle}",
                'enregistre_par' => $auteur->id,
            ]);

            $utilisation = UtilisationRecompense::create([
                'client_id' => $client->id,
                'recompense_id' => $recompense->id,
                'mouvement_points_id' => $mouvement->id,
                'enregistre_par' => $auteur->id,
                'utilisee_le' => now(),
            ]);

            $this->audit->log(
                'fidelite.recompense',
                $client,
                nouvelles: ['recompense' => $recompense->libelle]
            );

            return $utilisation;
        });
    }

    /**
     * Effectue une correction manuelle du solde.
     */
    public function corriger(
        Client $client,
        int $points,
        string $motif,
        User $auteur
    ): MouvementPoints {
        return DB::transaction(function () use ($client, $points, $motif, $auteur) {
            $client = Client::query()
                ->lockForUpdate()
                ->findOrFail($client->id);

            if ($points === 0) {
                throw new RegleMetierException(
                    'La correction doit être différente de zéro.',
                    'points'
                );
            }

            if ($client->soldePoints() + $points < 0) {
                throw new RegleMetierException(
                    'La correction rendrait le solde négatif.',
                    'points'
                );
            }

            $mouvement = MouvementPoints::create([
                'client_id' => $client->id,
                'type' => TypeMouvementPoints::Correction,
                'points' => $points,
                'motif' => $motif,
                'enregistre_par' => $auteur->id,
            ]);

            $this->audit->log(
                'fidelite.correction',
                $client,
                nouvelles: [
                    'points' => $points,
                    'motif' => $motif,
                ]
            );

            return $mouvement;
        });
    }
}
