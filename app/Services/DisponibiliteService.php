<?php
namespace App\Services;

use App\Exceptions\RegleMetierException;
use App\Models\Employe;
use App\Models\RendezVous;
use Carbon\CarbonInterface;

class DisponibiliteService
{
    /**
     * Vérifier qu'un créneau est libre pour le professionnel.
     *
     * @throws RegleMetierException
     */
    public function verifier(
        Employe $employe,
        CarbonInterface $debut,
        CarbonInterface $fin,
        ?int $ignorerRendezVousId = null
    ): void {

        if (! $employe->is_active) {
            throw new RegleMetierException(
                "{$employe->nom_complet} n'est plus actif(ve).",
                'employe_id'
            );
        }

        if (! $debut->isSameDay($fin)) {
            throw new RegleMetierException(
                'Le rendez-vous doit se terminer le même jour.',
                'heure_debut'
            );
        }

        if (! $this->dansHorairesTravail($employe, $debut, $fin)) {
            throw new RegleMetierException(
                "Ce créneau est en dehors des horaires de travail de {$employe->nom_complet}.",
                'heure_debut'
            );
        }

        if ($this->aUnChevauchement(
            $employe,
            $debut,
            $fin,
            $ignorerRendezVousId
        )) {
            throw new RegleMetierException(
                "{$employe->nom_complet} a déjà un rendez-vous sur ce créneau.",
                'heure_debut'
            );
        }
    }

    /**
     * Vérifier les horaires du salon et de l'employé.
     */
    public function dansHorairesTravail(
        Employe $employe,
        CarbonInterface $debut,
        CarbonInterface $fin
    ): bool {

        // Vérifier si le salon est fermé ce jour-là.
        if (in_array(
            $debut->isoWeekday(),
            config('salon.jours_fermes', []),
            true
        )) {
            return false;
        }

        // Récupérer les disponibilités de l'employé.
        $disponibilite = $employe->disponibilites()
            ->where('jour_semaine', $debut->isoWeekday())
            ->first();

        if (! $disponibilite) {
            return false;
        }

        // Récupérer les horaires du salon.
        $ouvertureSalon = $this->heure(
            config('salon.heure_ouverture')
        );

        $fermetureSalon = $this->heure(
            config('salon.heure_fermeture')
        );

        // Vérifier les horaires de l'employé et du salon.
        return $debut->format('H:i:s') >= $ouvertureSalon
            && $debut->format('H:i:s') >= $this->heure($disponibilite->heure_debut)
            && $fin->format('H:i:s') <= $fermetureSalon
            && $fin->format('H:i:s') <= $this->heure($disponibilite->heure_fin);
    }

    /**
     * Vérifier si l'employé possède déjà un rendez-vous
     * qui chevauche le créneau demandé.
     */
    public function aUnChevauchement(
        Employe $employe,
        CarbonInterface $debut,
        CarbonInterface $fin,
        ?int $ignorerRendezVousId = null
    ): bool {

        return RendezVous::query()
            ->pourEmploye($employe->id)
            ->bloquants()
            ->where('debut', '<', $fin)
            ->where('fin', '>', $debut)
            ->when(
                $ignorerRendezVousId !== null,
                fn ($query) => $query->where('id', '!=', $ignorerRendezVousId)
            )
            ->exists();
    }

    /**
     * Normaliser une heure au format HH:MM:SS.
     */
    private function heure(string $heure): string
    {
        return strlen($heure) === 5
            ? "{$heure}:00"
            : $heure;
    }
}
