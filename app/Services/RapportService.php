<?php

namespace App\Services;

use App\Enums\StatutRendezVous;
use App\Models\Client;
use App\Models\Depense;
use App\Models\Paiement;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class RapportService
{
    public function generer(CarbonImmutable $debut, CarbonImmutable $fin, string $groupe): array
    {
        $termine = StatutRendezVous::Termine->value;
        $annule = StatutRendezVous::Annule->value;
        $jours = [$debut->toDateString(), $fin->toDateString()];

        // ── Recettes, dépenses et visites, jour par jour ──
        $recettes = Paiement::valides()
            ->whereBetween('paye_le', [$debut, $fin])
            ->selectRaw('DATE(paye_le) as jour, SUM(montant_paye) as total')
            ->groupBy('jour')
            ->pluck('total', 'jour')
            ->all();

        $depenses = Depense::valides()
            ->whereBetween('date_depense', $jours)
            ->selectRaw('date_depense as jour, SUM(montant) as total')
            ->groupBy('date_depense')
            ->pluck('total', 'jour')
            ->all();

        $visites = DB::table('rendez_vous')
            ->where('statut', $termine)
            ->whereBetween('debut', [$debut, $fin])
            ->selectRaw('DATE(debut) as jour, COUNT(*) as total')
            ->groupBy('jour')
            ->pluck('total', 'jour')
            ->all();

        $serie = $this->serie($debut, $fin, $groupe, $recettes, $depenses, $visites);

        $totaux = [
            'recettes' => (int) collect($serie)->sum('recettes'),
            'depenses' => (int) collect($serie)->sum('depenses'),
            'visites' => (int) collect($serie)->sum('visites'),
        ];
        $totaux['resultat'] = $totaux['recettes'] - $totaux['depenses'];

        // ── Repères du chiffre d'affaires (indépendants du filtre) ──
        $maintenant = CarbonImmutable::now();
        $reperes = [
            'jour' => $this->recettesEntre($maintenant->startOfDay(), $maintenant),
            'semaine' => $this->recettesEntre($maintenant->startOfWeek(), $maintenant),
            'mois' => $this->recettesEntre($maintenant->startOfMonth(), $maintenant),
        ];

        // ── Dépenses par catégorie ──
        $depensesCategories = DB::table('depenses as d')
            ->join('categories_depenses as c', 'c.id', '=', 'd.categorie_depense_id')
            ->whereNull('d.annule_le')
            ->whereBetween('d.date_depense', $jours)
            ->selectRaw('c.nom as libelle, COUNT(*) as nombre, SUM(d.montant) as total')
            ->groupBy('c.nom')
            ->orderByDesc('total')
            ->get();

        // ── Prestations les plus demandées ──
        $prestations = DB::table('rendez_vous_service as ligne')
            ->join('rendez_vous as r', 'r.id', '=', 'ligne.rendez_vous_id')
            ->where('r.statut', $termine)
            ->whereBetween('r.debut', [$debut, $fin])
            ->selectRaw('ligne.libelle, COUNT(*) as total, SUM(ligne.prix) as montant')
            ->groupBy('ligne.libelle')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // ── Répartition des modes de paiement ──
        $modes = DB::table('paiements as p')
            ->join('modes_paiement as m', 'm.id', '=', 'p.mode_paiement_id')
            ->whereNull('p.annule_le')
            ->whereBetween('p.paye_le', [$debut, $fin])
            ->selectRaw('m.libelle, COUNT(*) as nombre, SUM(p.montant_paye) as total')
            ->groupBy('m.libelle')
            ->orderByDesc('total')
            ->get();

        // ── Clientèle ──
        $nouveaux = Client::query()
            ->nonArchives()
            ->whereBetween('date_inscription', $jours)
            ->count();

        $recurrents = DB::table('rendez_vous as r')
            ->join('clients as c', 'c.id', '=', 'r.client_id')
            ->whereNull('c.archived_at')
            ->where('r.statut', $termine)
            ->whereBetween('r.debut', [$debut, $fin])
            ->whereDate('c.date_inscription', '<', $debut->toDateString())
            ->distinct()
            ->count('c.id');

        // ── Rendez-vous : statuts et taux d'annulation ──
        $parStatut = DB::table('rendez_vous')
            ->whereBetween('debut', [$debut, $fin])
            ->selectRaw('statut, COUNT(*) as total')
            ->groupBy('statut')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($ligne) => [
                'statut' => $ligne->statut,
                'libelle' => StatutRendezVous::tryFrom($ligne->statut)?->label() ?? $ligne->statut,
                'total' => (int) $ligne->total,
            ]);

        $totalRdv = (int) $parStatut->sum('total');
        $nbAnnules = (int) $parStatut->where('statut', $annule)->sum('total');

        // ── Performances des employés ──
        $encaisseParEmploye = DB::table('paiements as p')
            ->join('rendez_vous as r', 'r.id', '=', 'p.rendez_vous_id')
            ->whereNull('p.annule_le')
            ->whereBetween('p.paye_le', [$debut, $fin])
            ->selectRaw('r.employe_id, SUM(p.montant_paye) as total')
            ->groupBy('r.employe_id')
            ->pluck('total', 'employe_id')
            ->all();

        $employes = DB::table('rendez_vous as r')
            ->join('employes as e', 'e.id', '=', 'r.employe_id')
            ->whereBetween('r.debut', [$debut, $fin])
            ->selectRaw(
                'e.id, e.prenom, e.nom, e.taux_commission, COUNT(*) as total_rdv,
                 SUM(CASE WHEN r.statut = ? THEN 1 ELSE 0 END) as termines,
                 SUM(CASE WHEN r.statut = ? THEN 1 ELSE 0 END) as annules',
                [$termine, $annule]
            )
            ->groupBy('e.id', 'e.prenom', 'e.nom', 'e.taux_commission')
            ->get()
            ->map(function ($e) use ($encaisseParEmploye) {
                $encaisse = (int) ($encaisseParEmploye[$e->id] ?? 0);

                return [
                    'nom' => trim($e->prenom . ' ' . $e->nom),
                    'rdv' => (int) $e->total_rdv,
                    'termines' => (int) $e->termines,
                    'annules' => (int) $e->annules,
                    'encaisse' => $encaisse,
                    'taux' => $e->taux_commission,
                    'commission' => $e->taux_commission !== null
                        ? (int) round($encaisse * $e->taux_commission / 100)
                        : null,
                ];
            })
            ->sortByDesc('encaisse')
            ->values();

        return [
            'serie' => $serie,
            'totaux' => $totaux,
            'reperes' => $reperes,
            'depensesCategories' => $depensesCategories,
            'prestations' => $prestations,
            'modes' => $modes,
            'employes' => $employes,
            'clients' => ['nouveaux' => $nouveaux, 'recurrents' => (int) $recurrents],
            'rendezVous' => [
                'total' => $totalRdv,
                'annules' => $nbAnnules,
                'taux' => $totalRdv > 0 ? round($nbAnnules / $totalRdv * 100, 1) : null,
                'parStatut' => $parStatut,
            ],
        ];
    }

    private function recettesEntre(CarbonImmutable $debut, CarbonImmutable $fin): int
    {
        return (int) Paiement::valides()->whereBetween('paye_le', [$debut, $fin])->sum('montant_paye');
    }

    /** Regroupe les valeurs journalières par jour, semaine ou mois. */
    private function serie(CarbonImmutable $debut, CarbonImmutable $fin, string $groupe, array $recettes, array $depenses, array $visites): array
    {
        $groupes = [];

        for ($d = $debut->startOfDay(); $d->lte($fin); $d = $d->addDay()) {
            $date = $d->toDateString();
            $lundi = $d->startOfWeek();

            [$cle, $libelle, $court] = match ($groupe) {
                'semaine' => [$lundi->toDateString(), 'Semaine du ' . $lundi->format('d/m/Y'), $lundi->format('d/m')],
                'mois' => [
                    $d->format('Y-m'),
                    ucfirst($d->locale('fr')->translatedFormat('F Y')),
                    $d->locale('fr')->translatedFormat('M y'),
                ],
                default => [$date, $d->format('d/m/Y'), $d->format('d/m')],
            };

            $groupes[$cle] ??= [
                'libelle' => $libelle,
                'court' => $court,
                'recettes' => 0,
                'depenses' => 0,
                'visites' => 0,
            ];

            $groupes[$cle]['recettes'] += (int) ($recettes[$date] ?? 0);
            $groupes[$cle]['depenses'] += (int) ($depenses[$date] ?? 0);
            $groupes[$cle]['visites'] += (int) ($visites[$date] ?? 0);
        }

        return array_values(array_map(function ($ligne) {
            $ligne['resultat'] = $ligne['recettes'] - $ligne['depenses'];

            return $ligne;
        }, $groupes));
    }
}