<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnnulerDepenseRequest;
use App\Http\Requests\StoreDepenseRequest;
use App\Models\CategorieDepense;
use App\Models\Depense;
use App\Models\Paiement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DepenseController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'mois' => ['nullable', 'date_format:Y-m'],
            'jour' => ['nullable', 'date'],
        ]);

        // Un jour choisi remplace le mois : vue journalière ou mensuelle
        if ($request->filled('jour')) {
            $debut = Carbon::parse($request->jour)->startOfDay();
            $fin = $debut->copy()->endOfDay();
            $libelle = $debut->translatedFormat('d F Y');
            $mois = $debut->format('Y-m');
        } else {
            $mois = $request->input('mois') ?: now()->format('Y-m');
            $debut = Carbon::createFromFormat('Y-m-d', $mois . '-01')->startOfMonth();
            $fin = $debut->copy()->endOfMonth();
            $libelle = $debut->translatedFormat('F Y');
        }

        $periode = [$debut->toDateString(), $fin->toDateString()];

        // La liste montre TOUTES les dépenses (les annulées apparaissent barrées)
        $depenses = Depense::with(['categorie', 'auteur', 'annulePar'])
            ->whereBetween('date_depense', $periode)
            ->when($request->filled('categorie'), fn ($q) => $q->where('categorie_depense_id', $request->categorie))
            ->latest('date_depense')
            ->latest('id')
            ->get();
        
        // Les statistiques ne comptent QUE les dépenses valides
        $parJour = $depenses
            ->filter(fn ($d) => ! $d->estAnnulee())
            ->groupBy(fn ($d) => $d->date_depense->format('Y-m-d'))
            ->map(fn ($groupe) => $groupe->sum('montant'));

        // Totaux de la période : toujours SANS le filtre de catégorie
        $totalDepenses = Depense::valides()->whereBetween('date_depense', $periode)->sum('montant');
        $depensesAujourdhui = Depense::valides()->whereDate('date_depense', today())->sum('montant');

        // Recettes encaissées : paiements valides uniquement, montant payé (jamais le total)
        $recettes = Paiement::valides()->whereBetween('paye_le', [$debut, $fin])->sum('montant_paye');

        $resultat = $recettes - $totalDepenses;

        $categories = CategorieDepense::orderBy('nom')->get();

        return view('depenses.index', compact(
            'mois', 'libelle', 'depenses', 'parJour', 'totalDepenses',
            'depensesAujourdhui', 'recettes', 'resultat', 'categories'
        ));
    }

    public function create()
    {
        $categories = CategorieDepense::orderBy('nom')->get();

        return view('depenses.create', compact('categories'));
    }

    public function store(StoreDepenseRequest $request)
    {
        $data = $request->safe()->except('justificatif');
        $data['enregistre_par'] = $request->user()->id;

        if ($request->hasFile('justificatif')) {
            $data['justificatif_path'] = $request->file('justificatif')->store('justificatifs', 'public');
        }

        $depense = Depense::create($data);

        $this->journaliser($request->user()->id, 'depense.creee', $depense, null, $depense->toArray());

        return redirect()->route('depenses.index')->with('success', 'Dépense enregistrée.');
    }

    public function annuler(AnnulerDepenseRequest $request, Depense $depense)
    {
        DB::transaction(function () use ($request, $depense) {
            // On verrouille la ligne pour éviter une double annulation
            $depense = Depense::lockForUpdate()->findOrFail($depense->id);

            if ($depense->estAnnulee()) {
                throw ValidationException::withMessages([
                    'motif_annulation' => 'Cette dépense est déjà annulée.',
                ]);
            }

            $avant = $depense->toArray();

            $depense->update([
                'annule_le' => now(),
                'annule_par' => $request->user()->id,
                'motif_annulation' => $request->motif_annulation,
            ]);

            $this->journaliser(
                $request->user()->id, 'depense.annulee', $depense, $avant, $depense->fresh()->toArray()
            );
        });

        return redirect()->route('depenses.index')->with('success', 'Dépense annulée (trace conservée).');
    }

    private function journaliser(int $userId, string $action, Depense $depense, ?array $avant, ?array $apres): void
    {
        DB::table('historique_actions')->insert([
            'user_id' => $userId,
            'action' => $action,
            'auditable_type' => Depense::class,
            'auditable_id' => $depense->id,
            'anciennes_valeurs' => $avant ? json_encode($avant) : null,
            'nouvelles_valeurs' => $apres ? json_encode($apres) : null,
            'ip' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}