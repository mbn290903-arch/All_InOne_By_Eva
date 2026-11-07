<?php

namespace App\Services;

use App\Enums\StatutRendezVous;
use App\Exceptions\RegleMetierException;
use App\Models\Employe;
use App\Models\Forfait;
use App\Models\ParametreSalon;
use App\Models\RendezVous;
use App\Models\Service;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RendezVousService
{
    public function __construct(
        private readonly DisponibiliteService $disponibilites,
        private readonly FideliteService $fidelite,
        private readonly AuditLogger $audit,
    ) {}

    /**
     * Créer un rendez-vous.
     */
    public function creer(array $donnees, User $auteur): RendezVous
    {
        return DB::transaction(function () use ($donnees, $auteur) {

            [$employe, $lignes, $debut, $fin, $zone] =
                $this->preparer($donnees);

            $rendezVous = RendezVous::create([
                'client_id' => $donnees['client_id'],
                'employe_id' => $employe->id,
                'debut' => $debut,
                'fin' => $fin,
                'zone' => $zone,
                'prix_prevu' => $lignes->sum('prix'),
                'statut' => StatutRendezVous::EnAttente,
                'commentaire' => $donnees['commentaire'] ?? null,
                'cree_par' => $auteur->id,
            ]);

            $rendezVous->lignes()->createMany($lignes->all());

            $this->audit->log(
                'rdv.cree',
                $rendezVous,
                nouvelles: $rendezVous->only([
                    'debut',
                    'fin',
                    'employe_id',
                    'prix_prevu'
                ])
            );

            return $rendezVous;
        });
    }

    /**
     * Modifier un rendez-vous.
     */
    public function mettreAJour(
        RendezVous $rendezVous,
        array $donnees
    ): RendezVous {

        if (in_array(
            $rendezVous->statut,
            [StatutRendezVous::Termine, StatutRendezVous::Annule],
            true
        )) {
            throw new RegleMetierException(
                'Un rendez-vous terminé ou annulé ne peut plus être modifié.'
            );
        }

        return DB::transaction(function () use ($rendezVous, $donnees) {

            [$employe, $lignes, $debut, $fin, $zone] =
                $this->preparer($donnees, $rendezVous->id);

            $rendezVous->update([
                'client_id' => $donnees['client_id'],
                'employe_id' => $employe->id,
                'debut' => $debut,
                'fin' => $fin,
                'zone' => $zone,
                'prix_prevu' => $lignes->sum('prix'),
                'commentaire' => $donnees['commentaire'] ?? null,
            ]);

            $rendezVous->lignes()->delete();

            $rendezVous->lignes()->createMany($lignes->all());

            $this->audit->logChangements('rdv.modifie', $rendezVous);

            return $rendezVous;
        });
    }

    /**
     * Changer le statut d'un rendez-vous.
     */
    public function changerStatut(
        RendezVous $rendezVous,
        StatutRendezVous $statut
    ): RendezVous {

        $ancien = $rendezVous->statut;

        if ($ancien === $statut) {
            return $rendezVous;
        }

        DB::transaction(function () use ($rendezVous, $statut, $ancien) {

            $rendezVous->update([
                'statut' => $statut
            ]);

            $this->audit->log(
                'rdv.statut',
                $rendezVous,
                ['statut' => $ancien->value],
                ['statut' => $statut->value]
            );

            if ($statut === StatutRendezVous::Termine) {
                $this->fidelite->crediterSiEligible($rendezVous);
            }
        });

        return $rendezVous;
    }
    /**
     * Déplacer un rendez-vous à une nouvelle date/heure (même durée).
     * Un rendez-vous annulé ou absent repasse « En attente ».
     */
    public function deplacer(
        RendezVous $rendezVous,
        CarbonImmutable $nouveauDebut
    ): RendezVous {

        if (! $rendezVous->statut->peutEtreDeplace()) {
            throw new RegleMetierException(
                'Ce rendez-vous ne peut plus être déplacé.'
            );
        }

        return DB::transaction(function () use ($rendezVous, $nouveauDebut) {

            $employe = Employe::query()
                ->lockForUpdate()
                ->findOrFail($rendezVous->employe_id);

            $duree = (int) $rendezVous->debut->diffInMinutes($rendezVous->fin);
            $nouvelleFin = $nouveauDebut->addMinutes($duree);

            // Vérifie horaires, disponibilités et chevauchements
            // (en ignorant ce rendez-vous lui-même).
            $this->disponibilites->verifier(
                $employe,
                $nouveauDebut,
                $nouvelleFin,
                $rendezVous->id
            );

            $anciennes = [
                'debut' => $rendezVous->debut->toDateTimeString(),
                'fin' => $rendezVous->fin->toDateTimeString(),
                'statut' => $rendezVous->statut->value,
            ];

            $nouveauStatut = in_array(
                $rendezVous->statut,
                [StatutRendezVous::Annule, StatutRendezVous::Absent],
                true
            ) ? StatutRendezVous::EnAttente : $rendezVous->statut;

            $rendezVous->update([
                'debut' => $nouveauDebut,
                'fin' => $nouvelleFin,
                'statut' => $nouveauStatut,
            ]);

            $this->audit->log(
                'rdv.deplace',
                $rendezVous,
                $anciennes,
                [
                    'debut' => $nouveauDebut->toDateTimeString(),
                    'fin' => $nouvelleFin->toDateTimeString(),
                    'statut' => $nouveauStatut->value,
                ]
            );

            return $rendezVous;
        });
    }

    /**
     * Préparer toutes les données du rendez-vous.
     *
     * @return array{0: Employe, 1: Collection, 2: CarbonImmutable, 3: CarbonImmutable, 4: \App\Enums\ZoneSalon}
     */
    private function preparer(
        array $donnees,
        ?int $ignorerId = null
    ): array {

        $employe = Employe::query()
            ->lockForUpdate()
            ->findOrFail($donnees['employe_id']);

        // Récupérer les lignes et déterminer automatiquement la zone.
        [$lignes, $zone] = $this->lignes(
            $donnees['services'] ?? [],
            $donnees['forfaits'] ?? [],
            $employe,
            $donnees['zone'] ?? null

        );

        if ($lignes->isEmpty()) {
            throw new RegleMetierException(
                'Choisissez au moins une prestation ou un forfait.',
                'services'
            );
        }

        $debut = CarbonImmutable::parse(
            "{$donnees['date']} {$donnees['heure_debut']}"
        );

        $fin = $debut->addMinutes(
            $lignes->sum('duree_minutes')
                + (int) ParametreSalon::get('pause_minutes', 0)
        );

        // Vérifier les horaires et les disponibilités.
        $this->disponibilites->verifier(
            $employe,
            $debut,
            $fin,
            $ignorerId
        );

        return [$employe, $lignes, $debut, $fin, $zone];
    }

    /**
     * Récupérer les prestations, vérifier leur cohérence
     * et construire les lignes du rendez-vous.
     */
    private function lignes(
        array $serviceIds,
        array $forfaitIds,
        Employe $employe,
        ?string $zoneChoisie = null
    ): array {

        // 1. Récupérer les prestations individuelles actives.
        $services = Service::query()
            ->actifs()
            ->whereIn('id', $serviceIds)
            ->get();

        if ($services->count() !== count(array_unique($serviceIds))) {
            throw new RegleMetierException(
                'Une ou plusieurs prestations sont introuvables ou inactives.',
                'services'
            );
        }

        // 2. Récupérer les forfaits actifs et leurs prestations.
        $forfaits = Forfait::query()
            ->actifs()
            ->with('services')
            ->whereIn('id', $forfaitIds)
            ->get();

        if ($forfaits->count() !== count(array_unique($forfaitIds))) {
            throw new RegleMetierException(
                'Un ou plusieurs forfaits sont introuvables ou inactifs.',
                'forfaits'
            );
        }

        // 3. Récupérer toutes les prestations contenues dans les forfaits.
        $servicesForfaits = $forfaits->flatMap(
            fn(Forfait $forfait) => $forfait->services
        );

        foreach ($forfaits as $forfait) {
            if ($forfait->services->isEmpty()) {
                throw new RegleMetierException(
                    "Le forfait {$forfait->nom} ne contient aucune prestation.",
                    'forfaits'
                );
            }
        }

        // 4. Regrouper toutes les prestations.
        $toutesLesPrestations = $services->concat($servicesForfaits);

        // Si aucune prestation n'a été sélectionnée.
        if ($toutesLesPrestations->isEmpty()) {
            return [collect(), null];
        }

        // Vérifier les doublons.
        $idsToutesPrestations = $toutesLesPrestations->pluck('id');

        if (
            $idsToutesPrestations->count()
            !== $idsToutesPrestations->unique()->count()
        ) {
            throw new RegleMetierException(
                'Une même prestation ne peut pas être sélectionnée plusieurs fois.',
                'services'
            );
        }

        // Vérifier que toutes les prestations sont actives.
        if ($toutesLesPrestations->contains(
            fn(Service $service) => ! $service->is_active
        )) {
            throw new RegleMetierException(
                'Une prestation contenue dans un forfait est inactive.',
                'forfaits'
            );
        }

        // 5. Vérifier que toutes les prestations appartiennent à la même zone.
        $zones = $toutesLesPrestations
            ->pluck('zone')
            ->unique();

        if ($zones->count() !== 1) {
            throw new RegleMetierException(
                'Vous ne pouvez pas mélanger des prestations de zones différentes.',
                'zone'
            );
        }

        // Récupérer la zone commune.
        $zone = $zones->first();
        // La zone choisie doit correspondre à celle des prestations.
        $zoneValeur = $zone instanceof \BackedEnum ? $zone->value : $zone;

        if ($zoneChoisie !== null && $zoneChoisie !== $zoneValeur) {
            throw new RegleMetierException(
                'Les prestations choisies ne correspondent pas à la zone sélectionnée.',
                'zone'
            );
        }

        // 6. Vérifier les compétences de l'employé.
        $idsPrestations = $toutesLesPrestations
            ->pluck('id')
            ->unique()
            ->values();

        $nombrePrestationsMaitrisees = $employe->services()
            ->whereIn('services.id', $idsPrestations)
            ->distinct()
            ->count('services.id');

        if ($nombrePrestationsMaitrisees !== $idsPrestations->count()) {
            throw new RegleMetierException(
                'Cet employé ne maîtrise pas toutes les prestations sélectionnées.',
                'employe_id'
            );
        }

        // 7. Construire les lignes du rendez-vous.
        $lignes = $services->map(fn(Service $service) => [
            'service_id' => $service->id,
            'forfait_id' => null,
            'libelle' => $service->nom,
            'prix' => $service->prix,
            'duree_minutes' => $service->duree_minutes,
        ])->concat(
            $forfaits->map(fn(Forfait $forfait) => [
                'service_id' => null,
                'forfait_id' => $forfait->id,
                'libelle' => "Forfait {$forfait->nom}",
                'prix' => $forfait->prix,
                'duree_minutes' => $forfait->dureeTotale(),
            ])
        )->values();

        return [$lignes, $zone];
    }
}
