<?php
namespace App\Http\Controllers;

use App\Http\Requests\AnnulerPaiementRequest;
use App\Http\Requests\EncaisserPaiementRequest;
use App\Models\Client;
use App\Models\ModePaiement;
use App\Models\Paiement;
use App\Models\RendezVous;
use App\Services\CaisseService;
use Illuminate\Http\Request;

class CaisseController extends Controller
{
    public function __construct(private CaisseService $caisse) {}

    public function index(Request $request)
    {
        $query = Paiement::with(['client', 'mode', 'rendezVous', 'encaissePar'])
            ->latest('paye_le');

        if ($request->filled('du')) {
            $query->whereDate('paye_le', '>=', $request->du);
        }
        if ($request->filled('au')) {
            $query->whereDate('paye_le', '<=', $request->au);
        }
        if ($request->filled('mode')) {
            $query->where('mode_paiement_id', $request->mode);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('client', fn ($c) => $c
                ->where('nom', 'like', "%{$q}%")
                ->orWhere('prenom', 'like', "%{$q}%"));
        }
        if ($request->statut === 'annules') {
            $query->annules();
        } elseif ($request->statut !== 'tous') {
            $query->valides(); // par défaut : uniquement les paiements valides
        }

        $paiements = $query->paginate(15)->withQueryString();

        $recettesJour = Paiement::valides()->whereDate('paye_le', today())->sum('montant_paye');
        $recettesMois = Paiement::valides()
            ->whereYear('paye_le', now()->year)
            ->whereMonth('paye_le', now()->month)
            ->sum('montant_paye');
        $nbPaiementsJour = Paiement::valides()->whereDate('paye_le', today())->count();

        $modes = ModePaiement::where('is_active', true)->orderBy('libelle')->get();

        return view('caisse.index', compact(
            'paiements', 'recettesJour', 'recettesMois', 'nbPaiementsJour', 'modes'
        ));
    }

    public function create(Request $request)
    {
        $rendezVous = null;

        if ($request->filled('rendez_vous_id')) {
            $rendezVous = RendezVous::with(['client', 'lignes'])->findOrFail($request->rendez_vous_id);
        }

        $clients = Client::whereNull('archived_at')
            ->orderBy('nom')
            ->get(['id', 'prenom', 'nom', 'telephone']);

        $modes = ModePaiement::where('is_active', true)->orderBy('libelle')->get();

        return view('caisse.create', compact('rendezVous', 'clients', 'modes'));
    }

    public function store(EncaisserPaiementRequest $request)
    {
        $this->caisse->encaisser($request->validated(), $request->user());

        return redirect()->route('caisse.index')->with('success', 'Paiement enregistré.');
    }

    public function annuler(AnnulerPaiementRequest $request, Paiement $paiement)
    {
        $this->caisse->annuler($paiement, $request->motif_annulation, $request->user());

        return redirect()->route('caisse.index')->with('success', 'Paiement annulé.');
    }
}