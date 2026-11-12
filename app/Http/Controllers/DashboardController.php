<?php

namespace App\Http\Controllers;

use App\Enums\StatutRendezVous;
use App\Models\Client;
use App\Models\Depense;
use App\Models\MouvementStock;
use App\Models\Paiement;
use App\Models\Produit;
use App\Models\RendezVous;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function admin()
    {
        $maintenant = CarbonImmutable::now();
        $debutJour = $maintenant->startOfDay();
        $finJour = $maintenant->endOfDay();

        $debutMois = $maintenant->startOfMonth();
        $finMois = $maintenant->endOfMonth();
        $moisCourant = $maintenant->locale('fr')->translatedFormat('F Y');
        $debutMoisPrecedent = $debutMois->subMonth();
        $moisPrecedent = $debutMoisPrecedent
            ->locale('fr')
            ->translatedFormat('F Y');
        $finComparaison = $debutMoisPrecedent
            ->addDays(min($maintenant->day, $debutMoisPrecedent->daysInMonth) - 1)
            ->endOfDay();

        // -------------------------------------------------
        // RENDEZ-VOUS DU JOUR
        // -------------------------------------------------

        $actifsDuJour = RendezVous::query()
            ->whereBetween('debut', [$debutJour, $finJour])
            ->get(['id', 'statut'])
            ->filter(fn($rdv) => ! $rdv->statut->libereLeCreneau());

        $total = $actifsDuJour->count();

        $confirmes = $actifsDuJour->filter(fn($rdv) => in_array(
            $rdv->statut,
            [
                StatutRendezVous::Confirme,
                StatutRendezVous::ClientArrive,
                StatutRendezVous::EnCours,
                StatutRendezVous::Termine,
            ],
            true
        ))->count();

        $enAttente = $actifsDuJour
            ->where('statut', StatutRendezVous::EnAttente)
            ->count();

        $termines = $actifsDuJour
            ->where('statut', StatutRendezVous::Termine)
            ->count();

        $progression = $total > 0
            ? (int) round($termines / $total * 100)
            : 0;

        // -------------------------------------------------
        // RENDEZ-VOUS À VENIR
        // -------------------------------------------------

        $annule = StatutRendezVous::Annule->value;

        $aVenir = RendezVous::query()
            ->whereBetween('debut', [$maintenant, $maintenant->addDays(7)])
            ->where('statut', '!=', $annule)
            ->count();

        $precedents = RendezVous::query()
            ->whereBetween('debut', [$maintenant->subDays(7), $maintenant])
            ->where('statut', '!=', $annule)
            ->count();

        $evolution = $precedents > 0
            ? (int) round(($aVenir - $precedents) / $precedents * 100)
            : null;

        $prochains = RendezVous::query()
            ->with(['client', 'employe', 'lignes'])
            ->where('fin', '>=', $maintenant)
            ->where('debut', '<=', $finJour)
            ->bloquants()
            ->orderBy('debut')
            ->limit(5)
            ->get();

        $demain = $maintenant->addDay();

        $nonConfirmesDemain = RendezVous::query()
            ->with('client')
            ->whereBetween('debut', [
                $demain->startOfDay(),
                $demain->endOfDay(),
            ])
            ->where('statut', StatutRendezVous::EnAttente->value)
            ->orderBy('debut')
            ->get();

        // -------------------------------------------------
        // CLIENTÈLE
        // -------------------------------------------------

        $clients = Client::query()->nonArchives();

        $totalClients = (clone $clients)->count();

        $clientsActifs = (clone $clients)
            ->where('statut', 'actif')
            ->count();

        $nouveauxClients = (clone $clients)
            ->whereBetween('date_inscription', [$debutMois, $finMois])
            ->count();
        $clientsARecontacter = (clone $clients)->inactifsDepuis(60)->count();
        $nouveauxMoisPrecedent = (clone $clients)
            ->whereBetween('date_inscription', [
                $debutMoisPrecedent,
                $debutMoisPrecedent->endOfMonth(),
            ])
            ->count();

        $evolutionClients = $nouveauxClients - $nouveauxMoisPrecedent;

        // -------------------------------------------------
        // RECETTES ET PAIEMENTS
        // -------------------------------------------------

        $paiementsDuJour = Paiement::query()
            ->valides()
            ->whereBetween('paye_le', [$debutJour, $finJour]);

        $encaisseJour = (clone $paiementsDuJour)->sum('montant_paye');

        $nombrePaiementsJour = (clone $paiementsDuJour)->count();

        $paiementsPartiels = (clone $paiementsDuJour)
            ->where('reste_a_payer', '>', 0)
            ->count();

        $encaisseMois = Paiement::query()
            ->valides()
            ->whereBetween('paye_le', [$debutMois, $maintenant])
            ->sum('montant_paye');

        $encaisseMoisPrecedent = Paiement::query()
            ->valides()
            ->whereBetween('paye_le', [
                $debutMoisPrecedent,
                $finComparaison,
            ])
            ->sum('montant_paye');

        $evolutionRecettes = $encaisseMoisPrecedent > 0
            ? round(
                ($encaisseMois - $encaisseMoisPrecedent)
                    / $encaisseMoisPrecedent * 100,
                1
            )
            : null;

        // -------------------------------------------------
        // DÉPENSES ET RÉSULTAT
        // -------------------------------------------------

        $depensesMois = Depense::query()
            ->whereBetween('date_depense', [$debutMois, $maintenant])
            ->sum('montant');

        $resultatMois = $encaisseMois - $depensesMois;

        // -------------------------------------------------
        // GRAPHIQUE DES RECETTES PAR JOUR
        // -------------------------------------------------

        $recettesCourantes = Paiement::query()
            ->valides()
            ->whereBetween('paye_le', [$debutMois, $maintenant])
            ->selectRaw('DATE(paye_le) as jour, SUM(montant_paye) as total')
            ->groupBy('jour')
            ->pluck('total', 'jour');

        $recettesPrecedentes = Paiement::query()
            ->valides()
            ->whereBetween('paye_le', [
                $debutMoisPrecedent,
                $finComparaison,
            ])
            ->selectRaw('DATE(paye_le) as jour, SUM(montant_paye) as total')
            ->groupBy('jour')
            ->pluck('total', 'jour');

        $recettesGraphique = [];

        for ($jour = 1; $jour <= $maintenant->day; $jour++) {
            $date = $debutMois->day($jour)->toDateString();

            $datePrecedente = $debutMoisPrecedent
                ->day(min($jour, $debutMoisPrecedent->daysInMonth))
                ->toDateString();

            $recettesGraphique[] = [
                'jour' => $jour,
                'courant' => (int) ($recettesCourantes[$date] ?? 0),
                'precedent' => (int) ($recettesPrecedentes[$datePrecedente] ?? 0),
            ];
        }

        $maxRecetteGraphique = max(
            1,
            collect($recettesGraphique)->max('courant') ?? 0,
            collect($recettesGraphique)->max('precedent') ?? 0
        );

        // -------------------------------------------------
        // RÉPARTITION DES PRESTATIONS
        // -------------------------------------------------

        $repartitionPrestations = DB::table('rendez_vous_service as ligne')
            ->join('rendez_vous as rdv', 'rdv.id', '=', 'ligne.rendez_vous_id')
            ->join('services', 'services.id', '=', 'ligne.service_id')
            ->join('categories_services', 'categories_services.id', '=', 'services.categorie_service_id')
            ->where('rdv.statut', StatutRendezVous::Termine->value)
            ->whereBetween('rdv.debut', [$debutMois, $maintenant])
            ->selectRaw('categories_services.nom as libelle, COUNT(*) as total')
            ->groupBy('categories_services.nom')
            ->orderByDesc('total')
            ->get();

        $nombreForfaits = DB::table('rendez_vous_service as ligne')
            ->join('rendez_vous as rdv', 'rdv.id', '=', 'ligne.rendez_vous_id')
            ->whereNull('ligne.service_id')
            ->whereNotNull('ligne.forfait_id')
            ->where('rdv.statut', StatutRendezVous::Termine->value)
            ->whereBetween('rdv.debut', [$debutMois, $maintenant])
            ->count();

        if ($nombreForfaits > 0) {
            $repartitionPrestations->push((object) [
                'libelle' => 'Forfaits',
                'total' => $nombreForfaits,
            ]);
        }

        $totalPrestations = (int) $repartitionPrestations->sum('total');

        // -------------------------------------------------
        // SERVICES LES PLUS DEMANDÉS
        // -------------------------------------------------

        $servicesPopulaires = DB::table('rendez_vous_service as ligne')
            ->join('rendez_vous as rdv', 'rdv.id', '=', 'ligne.rendez_vous_id')
            ->whereNotNull('ligne.service_id')
            ->where('rdv.statut', StatutRendezVous::Termine->value)
            ->whereBetween('rdv.debut', [$debutMois, $maintenant])
            ->selectRaw('ligne.libelle, COUNT(*) as total')
            ->groupBy('ligne.libelle')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $maxService = max(1, (int) ($servicesPopulaires->max('total') ?? 0));

        // -------------------------------------------------
        // ÉVOLUTION DE LA CLIENTÈLE SUR SIX MOIS
        // -------------------------------------------------

        $evolutionClientele = [];

        for ($i = 5; $i >= 0; $i--) {
            $debut = $maintenant->startOfMonth()->subMonths($i);
            $fin = $debut->endOfMonth();

            $nouveaux = Client::query()
                ->nonArchives()
                ->whereBetween('date_inscription', [$debut, $fin])
                ->count();

            $recurrents = DB::table('rendez_vous')
                ->join('clients', 'clients.id', '=', 'rendez_vous.client_id')
                ->whereNull('clients.archived_at')
                ->where('rendez_vous.statut', StatutRendezVous::Termine->value)
                ->whereBetween('rendez_vous.debut', [$debut, $fin])
                ->whereDate('clients.date_inscription', '<', $debut->toDateString())
                ->distinct('clients.id')
                ->count('clients.id');

            $evolutionClientele[] = [
                'mois' => $debut->locale('fr')->translatedFormat('M'),
                'nouveaux' => $nouveaux,
                'recurrents' => $recurrents,
            ];
        }

        $maxClientele = max(
            1,
            collect($evolutionClientele)->max(
                fn($ligne) =>
                $ligne['nouveaux'] + $ligne['recurrents']
            )
        );

        // -------------------------------------------------
        // ALERTES : ANNIVERSAIRES
        // -------------------------------------------------

        $anniversaires = Client::query()
            ->nonArchives()
            ->whereNotNull('date_naissance')
            ->get(['id', 'prenom', 'nom', 'date_naissance'])
            ->map(function ($client) use ($maintenant) {
                $naissance = CarbonImmutable::parse($client->date_naissance);

                $jourAnniversaire = CarbonImmutable::create(
                    $maintenant->year,
                    $naissance->month,
                    min(
                        $naissance->day,
                        CarbonImmutable::create($maintenant->year, $naissance->month, 1)->daysInMonth
                    )
                );

                if ($jourAnniversaire->lt($maintenant->startOfDay())) {
                    $jourAnniversaire = $jourAnniversaire->addYear();
                }

                return [
                    'client' => $client,
                    'date' => $jourAnniversaire,
                ];
            })
            ->filter(
                fn($item) =>
                $item['date']->between(
                    $maintenant->startOfDay(),
                    $maintenant->addDays(7)->endOfDay()
                )
            )
            ->sortBy(fn($item) => $item['date']->timestamp)
            ->values();

        // -------------------------------------------------
        // ALERTES : STOCK
        // -------------------------------------------------

        $produitsEnAlerte = Produit::query()
            ->enAlerte()
            ->orderBy('quantite')
            ->get();

        // -------------------------------------------------
        // ALERTES : SOLDES IMPAYÉS
        // -------------------------------------------------

        $rendezVousImpayes = RendezVous::query()
            ->withSum([
                'paiements as montant_paye' => fn($query) => $query->valides(),
            ], 'montant_paye')
            ->where('statut', '!=', $annule)
            ->where('prix_prevu', '>', 0)
            ->get()
            ->map(function ($rdv) {
                $rdv->reste_dashboard = max(
                    0,
                    (int) $rdv->prix_prevu - (int) ($rdv->montant_paye ?? 0)
                );

                return $rdv;
            })
            ->filter(fn($rdv) => $rdv->reste_dashboard > 0)
            ->values();

        $nombreRendezVousImpayes = $rendezVousImpayes->count();

        $totalImpayes = (int) $rendezVousImpayes->sum('reste_dashboard');

        // -------------------------------------------------
        // ALERTES : NUMÉROS WHATSAPP EN DOUBLON
        // -------------------------------------------------

        $doublonsWhatsapp = DB::table('clients')
            ->select('whatsapp')
            ->whereNull('archived_at')
            ->whereNotNull('whatsapp')
            ->where('whatsapp', '!=', '')
            ->groupBy('whatsapp')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        // -------------------------------------------------
        // ACTIVITÉ RÉCENTE
        // -------------------------------------------------

        $activites = collect();

        Paiement::query()
            ->valides()
            ->with(['client', 'mode', 'encaissePar'])
            ->latest('paye_le')
            ->limit(5)
            ->get()
            ->each(function ($paiement) use ($activites) {
                $activites->push([
                    'date' => $paiement->paye_le,
                    'titre' => 'Paiement enregistré',
                    'detail' => ($paiement->client?->nomComplet ?? 'Client')
                        . ' · ' . number_format($paiement->montant_paye, 0, ',', ' ')
                        . ' FCFA · ' . ($paiement->mode?->libelle ?? 'Mode inconnu'),
                    'icone' => 'banknote',
                ]);
            });

        RendezVous::query()
            ->with('client')
            ->whereNotNull('updated_at')
            ->latest('updated_at')
            ->limit(5)
            ->get()
            ->each(function ($rdv) use ($activites) {
                $activites->push([
                    'date' => $rdv->updated_at,
                    'titre' => 'Rendez-vous : ' . $rdv->statut->label(),
                    'detail' => $rdv->client?->nomComplet ?? 'Client',
                    'icone' => 'calendar-check',
                ]);
            });

        Client::query()
            ->nonArchives()
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->each(function ($client) use ($activites) {
                $activites->push([
                    'date' => $client->created_at,
                    'titre' => 'Nouvelle cliente enregistrée',
                    'detail' => $client->nomComplet,
                    'icone' => 'user-plus',
                ]);
            });

        MouvementStock::query()
            ->with(['produit', 'auteur'])
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->each(function ($mouvement) use ($activites) {
                $activites->push([
                    'date' => $mouvement->created_at,
                    'titre' => 'Mouvement de stock : ' . $mouvement->type->value,
                    'detail' => ($mouvement->produit?->nom ?? 'Produit')
                        . ' · ' . $mouvement->quantite . ' '
                        . ($mouvement->produit?->unite ?? ''),
                    'icone' => 'package',
                ]);
            });

        Depense::query()
            ->with('categorie')
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->each(function ($depense) use ($activites) {
                $activites->push([
                    'date' => $depense->created_at,
                    'titre' => 'Dépense enregistrée',
                    'detail' => $depense->description
                        . ' · ' . number_format($depense->montant, 0, ',', ' ')
                        . ' FCFA',
                    'icone' => 'receipt',
                ]);
            });

        $activitesRecentes = $activites
            ->filter(fn($item) => $item['date'] !== null)
            ->sortByDesc(fn($item) => $item['date']->timestamp)
            ->take(6)
            ->values();

        $nombreAlertes = $nonConfirmesDemain->count()
            + $anniversaires->count()
            + $produitsEnAlerte->count()
            + $nombreRendezVousImpayes
            + $doublonsWhatsapp;

        return view('admin.dashboard', compact(
            'maintenant',
            'moisCourant',
            'moisPrecedent',
            'total',
            'confirmes',
            'enAttente',
            'termines',
            'progression',
            'aVenir',
            'evolution',
            'prochains',
            'nonConfirmesDemain',
            'totalClients',
            'clientsActifs',
            'nouveauxClients',
            'evolutionClients',
            'clientsARecontacter',
            'encaisseJour',
            'nombrePaiementsJour',
            'paiementsPartiels',
            'encaisseMois',
            'evolutionRecettes',
            'depensesMois',
            'resultatMois',
            'recettesGraphique',
            'maxRecetteGraphique',
            'repartitionPrestations',
            'totalPrestations',
            'servicesPopulaires',
            'maxService',
            'evolutionClientele',
            'maxClientele',
            'anniversaires',
            'produitsEnAlerte',
            'nombreRendezVousImpayes',
            'totalImpayes',
            'doublonsWhatsapp',
            'activitesRecentes',
            'nombreAlertes'
        ));
    }
}
