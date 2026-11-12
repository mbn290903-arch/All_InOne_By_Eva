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
use Illuminate\Support\Facades\DB;

class CaisseController extends Controller
{
    public function __construct(private CaisseService $caisse) {}

    public function index(Request $request)
    {
        $base = Paiement::query()
            ->when($request->filled('du'), fn ($q) => $q->whereDate('paye_le', '>=', $request->du))
            ->when($request->filled('au'), fn ($q) => $q->whereDate('paye_le', '<=', $request->au))
            ->when($request->filled('mode'), fn ($q) => $q->where('mode_paiement_id', $request->mode))
            ->when($request->filled('q'), fn ($q) => $q->whereHas('client', fn ($c) => $c->where(
                fn ($w) => $w->where('nom', 'like', "%{$request->q}%")
                    ->orWhere('prenom', 'like', "%{$request->q}%")
            )));

        // Les statistiques ne comptent QUE les paiements valides et le montant PAYÉ
        $totalPeriode = (clone $base)->valides()->sum('montant_paye');

        $liste = (clone $base)
            ->with(['client', 'mode', 'rendezVous.lignes', 'encaissePar', 'annulePar'])
            ->latest('paye_le');

        if ($request->statut === 'annules') {
            $liste->annules();
        } elseif ($request->statut !== 'tous') {
            $liste->valides();
        }

        $paiements = $liste->paginate(15)->withQueryString();

        $recettesJour = Paiement::valides()->whereDate('paye_le', today())->sum('montant_paye');
        $recettesMois = Paiement::valides()
            ->whereYear('paye_le', now()->year)
            ->whereMonth('paye_le', now()->month)
            ->sum('montant_paye');
        $nbPaiementsJour = Paiement::valides()->whereDate('paye_le', today())->count();

        $modes = ModePaiement::where('is_active', true)->orderBy('libelle')->get();

        return view('caisse.index', compact(
            'paiements', 'totalPeriode', 'recettesJour', 'recettesMois', 'nbPaiementsJour', 'modes'
        ));
    }

    public function create(Request $request)
    {
        $rdvs = $this->rdvAvecReste(false);
        $selectedId = $request->input('rendez_vous_id');

        $clients = Client::whereNull('archived_at')
            ->orderBy('nom')
            ->get(['id', 'prenom', 'nom', 'telephone']);

        $modes = ModePaiement::where('is_active', true)->orderBy('libelle')->get();

        return view('caisse.create', compact('rdvs', 'selectedId', 'clients', 'modes'));
    }

    public function store(EncaisserPaiementRequest $request)
    {
        $paiement = $this->caisse->encaisser($request->validated(), $request->user());

        return redirect()->route('caisse.recu', $paiement)->with('success', 'Paiement enregistré.');
    }

    public function annuler(AnnulerPaiementRequest $request, Paiement $paiement)
    {
        abort_unless(
            $request->user()->aPermission('paiements.annuler'),
            403,
            "Vous n'avez pas la permission d'annuler un paiement."
        );

        $this->caisse->annuler($paiement, $request->motif_annulation, $request->user());

        // « Corriger » = annuler puis ressaisir le paiement
        if ($request->boolean('corriger')) {
            return redirect()->route('caisse.create', array_filter([
                'rendez_vous_id' => $paiement->rendez_vous_id,
            ]))->with('success', 'Paiement annulé. Saisissez le paiement corrigé.');
        }

        return redirect()->route('caisse.index')->with('success', 'Paiement annulé.');
    }

    public function recu(Paiement $paiement)
    {
        $paiement->load(['client', 'mode', 'encaissePar', 'annulePar', 'rendezVous.lignes', 'rendezVous.employe']);

        // Tous les paiements valides du rendez-vous, jusqu'à celui-ci inclus
        if ($paiement->rendez_vous_id && ! $paiement->estAnnule()) {
            $paiements = Paiement::valides()
                ->with('mode')
                ->where('rendez_vous_id', $paiement->rendez_vous_id)
                ->where(fn ($q) => $q
                    ->where('paye_le', '<', $paiement->paye_le)
                    ->orWhere(fn ($m) => $m
                        ->where('paye_le', $paiement->paye_le)
                        ->where('id', '<=', $paiement->id)))
                ->orderBy('paye_le')
                ->orderBy('id')
                ->get();
        } else {
            $paiements = collect([$paiement]);
        }

        $totalDu = (int) $paiement->montant_total;
        $totalPaye = (int) $paiements->sum('montant_paye');
        $reste = max(0, $totalDu - $totalPaye);

        // Informations du salon (table parametres_salon)
        $salon = DB::table('parametres_salon')->pluck('valeur', 'cle');
        $nomSalon = $salon->get('nom', 'All in One by Eva');
        $adresseSalon = $salon->get('adresse');
        $telephoneSalon = $salon->get('telephone');

        // Numéros d'affichage
        $numeroRecu = 'REC-' . $paiement->paye_le->format('Y') . '-' . str_pad($paiement->id, 5, '0', STR_PAD_LEFT);
        $codeClient = 'CL-' . str_pad($paiement->client_id, 6, '0', STR_PAD_LEFT);
        $codeRdv = $paiement->rendez_vous_id
            ? 'R-' . str_pad($paiement->rendez_vous_id, 5, '0', STR_PAD_LEFT)
            : null;

        // Points de fidélité (affichés seulement si le paiement en a généré)
        $mouvementPoints = DB::table('mouvements_points')->where('paiement_id', $paiement->id)->first();
        $pointsGagnes = $mouvementPoints?->points;
        $soldePoints = $mouvementPoints
            ? (int) DB::table('mouvements_points')
                ->where('client_id', $paiement->client_id)
                ->where('id', '<=', $mouvementPoints->id)
                ->sum('points')
            : null;

        // Lien WhatsApp (message prérempli)
        $numero = preg_replace('/\D/', '', $paiement->client->whatsapp ?: $paiement->client->telephone);
        if (strlen($numero) === 9) {
            $numero = '221' . $numero; // indicatif du Sénégal
        }
        $format = fn ($n) => number_format($n, 0, ',', ' ');
        $texte = "Bonjour {$paiement->client->prenom}, voici votre reçu {$numeroRecu} ({$nomSalon}) : "
            . $format($totalPaye) . ' FCFA payés sur ' . $format($totalDu) . ' FCFA'
            . ($reste > 0 ? ', reste à payer : ' . $format($reste) . ' FCFA' : ', paiement soldé')
            . '. Merci de votre visite !';
        $lienWhatsapp = $numero ? 'https://wa.me/' . $numero . '?text=' . rawurlencode($texte) : null;

        return view('caisse.recu', compact(
            'paiement', 'paiements', 'totalDu', 'totalPaye', 'reste',
            'nomSalon', 'adresseSalon', 'telephoneSalon',
            'numeroRecu', 'codeClient', 'codeRdv',
            'pointsGagnes', 'soldePoints', 'lienWhatsapp'
        ));
    }

    public function impayes()
    {
        $impayes = $this->rdvAvecReste(true);
        $totalImpaye = $impayes->sum(fn ($r) => $r->prix_prevu - (int) $r->paye);

        return view('caisse.impayes', compact('impayes', 'totalImpaye'));
    }

    // ── Modes de paiement configurables (admin) ──

    public function modes()
    {
        $modes = ModePaiement::orderBy('libelle')->get();

        return view('caisse.modes', compact('modes'));
    }

    public function storeMode(Request $request)
    {
        $data = $request->validate([
            'libelle' => ['required', 'string', 'max:100', 'unique:modes_paiement,libelle'],
        ], [
            'libelle.required' => 'Saisissez le nom du mode de paiement.',
            'libelle.unique' => 'Ce mode existe déjà.',
        ]);

        ModePaiement::create($data);

        return back()->with('success', 'Mode de paiement ajouté.');
    }

    public function toggleMode(ModePaiement $mode)
    {
        $mode->update(['is_active' => ! $mode->is_active]);

        return back()->with('success', 'Mode de paiement mis à jour.');
    }

        /** Rendez-vous non annulés ayant un reste à payer. */
    private function rdvAvecReste(bool $passesSeulement)
    {
        return RendezVous::with(['client', 'lignes'])
            ->bloquants()
            ->withSum(['paiements as paye' => fn ($q) => $q->whereNull('annule_le')], 'montant_paye')
            ->when($passesSeulement, fn ($q) => $q->where('debut', '<=', now()))
            ->whereRaw('rendez_vous.prix_prevu > COALESCE((
                select sum(paiements.montant_paye)
                from paiements
                where paiements.rendez_vous_id = rendez_vous.id
                  and paiements.annule_le is null
            ), 0)')
            ->orderBy('debut')
            ->get();
    }
}