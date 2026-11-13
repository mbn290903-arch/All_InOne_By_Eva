<?php

namespace App\Http\Controllers;

use App\Services\RapportService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RapportController extends Controller
{
    public function __construct(private RapportService $rapports) {}

    public function index(Request $request)
    {
        return view('rapports.index', $this->contexte($request));
    }

    /** Page propre pour imprimer ou enregistrer en PDF. */
    public function imprimer(Request $request)
    {
        return view('rapports.imprimer', $this->contexte($request));
    }

    public function exporter(Request $request)
    {
        ['debut' => $debut, 'fin' => $fin, 'rapport' => $r] = $this->contexte($request);

        $nom = 'rapport_' . $debut->format('Ymd') . '_' . $fin->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($r, $debut, $fin) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // pour que Excel lise bien les accents
            $ecrire = fn (array $colonnes) => fputcsv($out, $colonnes, ';', '"', '');

            $ecrire(['Rapport All in One by Eva']);
            $ecrire(['Période', $debut->format('d/m/Y') . ' au ' . $fin->format('d/m/Y')]);
            $ecrire(['Généré le', now()->format('d/m/Y H:i')]);
            $ecrire([]);

            $ecrire(['SYNTHÈSE (montants en FCFA)']);
            $ecrire(['Recettes encaissées', $r['totaux']['recettes']]);
            $ecrire(['Dépenses enregistrées', $r['totaux']['depenses']]);
            $ecrire(['Résultat estimé (indicateur de gestion, pas un bénéfice comptable)', $r['totaux']['resultat']]);
            $ecrire(['Visites (rendez-vous terminés)', $r['totaux']['visites']]);
            $ecrire(['Nouveaux clients', $r['clients']['nouveaux']]);
            $ecrire(['Clients récurrents', $r['clients']['recurrents']]);
            $ecrire(['Rendez-vous sur la période', $r['rendezVous']['total']]);
            $ecrire(['Rendez-vous annulés', $r['rendezVous']['annules']]);
            $ecrire(['Taux d\'annulation (%)', $r['rendezVous']['taux'] ?? 'n/a']);
            $ecrire([]);

            $ecrire(['ÉVOLUTION']);
            $ecrire(['Période', 'Recettes', 'Dépenses', 'Résultat estimé', 'Visites']);
            foreach ($r['serie'] as $l) {
                $ecrire([$l['libelle'], $l['recettes'], $l['depenses'], $l['resultat'], $l['visites']]);
            }
            $ecrire([]);

            $ecrire(['DÉPENSES PAR CATÉGORIE']);
            $ecrire(['Catégorie', 'Nombre', 'Total']);
            foreach ($r['depensesCategories'] as $l) {
                $ecrire([$l->libelle, $l->nombre, $l->total]);
            }
            $ecrire([]);

            $ecrire(['PRESTATIONS LES PLUS DEMANDÉES']);
            $ecrire(['Prestation', 'Nombre', 'Montant']);
            foreach ($r['prestations'] as $l) {
                $ecrire([$l->libelle, $l->total, $l->montant]);
            }
            $ecrire([]);

            $ecrire(['MODES DE PAIEMENT']);
            $ecrire(['Mode', 'Paiements', 'Total encaissé']);
            foreach ($r['modes'] as $l) {
                $ecrire([$l->libelle, $l->nombre, $l->total]);
            }
            $ecrire([]);

            $ecrire(['PERFORMANCES DES EMPLOYÉS']);
            $ecrire(['Employé', 'Rendez-vous', 'Terminés', 'Annulés', 'Encaissé', 'Taux de commission (%)', 'Commission estimée']);
            foreach ($r['employes'] as $l) {
                $ecrire([$l['nom'], $l['rdv'], $l['termines'], $l['annules'], $l['encaisse'], $l['taux'] ?? '', $l['commission'] ?? '']);
            }
            $ecrire([]);

            $ecrire(['RENDEZ-VOUS PAR STATUT']);
            $ecrire(['Statut', 'Nombre']);
            foreach ($r['rendezVous']['parStatut'] as $l) {
                $ecrire([$l['libelle'], $l['total']]);
            }

            fclose($out);
        }, $nom, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function contexte(Request $request): array
    {
        $request->validate([
            'du' => ['nullable', 'date'],
            'au' => ['nullable', 'date', 'after_or_equal:du'],
            'groupe' => ['nullable', 'in:jour,semaine,mois'],
        ], [
            'du.date' => 'La date de début est invalide.',
            'au.date' => 'La date de fin est invalide.',
            'au.after_or_equal' => 'La date de fin doit être après la date de début.',
            'groupe.in' => 'Le regroupement doit être jour, semaine ou mois.',
        ]);

        $fin = $request->filled('au')
            ? CarbonImmutable::parse($request->au)->endOfDay()
            : CarbonImmutable::now()->endOfDay();

        $debut = $request->filled('du')
            ? CarbonImmutable::parse($request->du)->startOfDay()
            : CarbonImmutable::now()->startOfMonth();

        if ($debut->gt($fin)) {
            throw ValidationException::withMessages(['du' => 'La date de début est après la date de fin.']);
        }
        if ($debut->diffInDays($fin) > 731) {
            throw ValidationException::withMessages(['du' => 'La période ne peut pas dépasser 2 ans.']);
        }

        $groupe = $request->input('groupe', 'jour');

        return [
            'debut' => $debut,
            'fin' => $fin,
            'groupe' => $groupe,
            'rapport' => $this->rapports->generer($debut, $fin, $groupe),
        ];
    }
}