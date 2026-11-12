<?php
namespace App\Http\Controllers;

use App\Enums\TypeMouvementPoints;
use App\Enums\TypeRecompense;
use App\Models\Client;
use App\Models\MouvementPoints;
use App\Models\Recompense;
use App\Models\RegleFidelite;
use App\Models\UtilisationRecompense;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
class FideliteController extends Controller
{
    public function index()
    {
        $clients = Client::query()
            ->nonArchives()
            ->with('mouvementsPoints')
            ->orderBy('nom')
            ->orderBy('prenom')
            ->paginate(20);

        $clients->getCollection()->transform(function ($client) {
            $client->solde_points = $client->mouvementsPoints->sum("points");
            $client->points_gagnes = $client->mouvementsPoints
                ->where('type', TypeMouvementPoints::Gain)->sum('points');
            $client->points_utilises = abs($client->mouvementsPoints
                ->where('type', TypeMouvementPoints::Utilisation)->sum('points'));

            return $client;
        });

        $meilleursClients = Client::query()
            ->nonArchives()
            ->withSum('mouvementsPoints as solde_points', 'points')
            ->orderByDesc('solde_points')
            ->take(5)
            ->get();

        $mouvements = MouvementPoints::with([
            'client',
            'paiement',
            'auteur',
            'regleFidelite',
        ])->latest()->paginate(30);

        $activiteRecente = MouvementPoints::with('client')
            ->latest()
            ->limit(5)
            ->get();

        $regles = RegleFidelite::query()
            ->orderByDesc('date_debut')
            ->get();

        $regleActuelle = RegleFidelite::enVigueur()
            ->orderByDesc('date_debut')
            ->first();

        $recompenses = Recompense::query()
            ->orderBy('seuil_points')
            ->get();

        $clientsFidelises = Client::nonArchives()
            ->whereHas('mouvementsPoints')
            ->count();

        $pointsDistribues = MouvementPoints::query()
            ->where('type', TypeMouvementPoints::Gain)
            ->sum('points');

        $recompensesUtilisees = UtilisationRecompense::query()
            ->whereMonth('utilisee_le', now()->month)
            ->whereYear('utilisee_le', now()->year)
            ->count();

        return view('admin.fidelite', compact(
            'clients',
            'meilleursClients',
            'mouvements',
            'activiteRecente',
            'regles',
            'regleActuelle',
            'recompenses',
            'clientsFidelises',
            'pointsDistribues',
            'recompensesUtilisees'
        ));
    }

    public function storeRegle(Request $request)
    {
        $donnees = $request->validate([
            'libelle' => ['required', 'string', 'max:120'],
            'montant_par_tranche' => ['required', 'integer', 'min:1'],
            'points_par_tranche' => ['required', 'integer', 'min:1'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
        ]);

        $donnees['is_active'] = true;
        $donnees['enregistre_par'] = $request->user()->id;

        RegleFidelite::create($donnees);

        return back()->with('success', 'La règle de fidélité a été enregistrée.');
    }

    public function storeRecompense(Request $request)
    {
        $donnees = $request->validate([
            'libelle' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:' . implode(',', array_map(
                fn ($type) => $type->value,
                TypeRecompense::cases()
            ))],
            'seuil_points' => ['required', 'integer', 'min:1'],
            'valeur' => ['nullable', 'integer', 'min:0'],
        ]);

        $donnees['valeur'] = $donnees['valeur'] ?? 0;
        $donnees['is_active'] = true;

        Recompense::create($donnees);

        return back()->with('success', 'La récompense a été ajoutée.');
    }

     
public function updateRegle(Request $request, RegleFidelite $regle)
{
    $data = $request->validate([
        'libelle'             => ['required', 'string', 'max:120'],
        'montant_par_tranche' => ['required', 'integer', 'min:1'],
        'points_par_tranche'  => ['required', 'integer', 'min:1'],
        'date_debut'          => ['required', 'date'],
        'date_fin'            => ['nullable', 'date', 'after_or_equal:date_debut'],
    ]);
 
    $data['is_active'] = $request->boolean('is_active');
 
    $regle->update($data);
 
    return redirect()->to(url()->previous() . '#overview')
        ->with('success', 'Règle mise à jour. Elle ne s\'applique qu\'aux paiements futurs.');
}
 
public function destroyRecompense(Recompense $recompense)
{
    try {
        $recompense->delete();
    } catch (QueryException $e) {
        // Récompense déjà utilisée : on la désactive pour garder l'historique.
        $recompense->update(['is_active' => false]);
 
        return redirect()->to(url()->previous() . '#rewards')
            ->with('success', 'Cette récompense a déjà été utilisée : elle a été désactivée au lieu d\'être supprimée.');
    }
 
    return redirect()->to(url()->previous() . '#rewards')
        ->with('success', 'Récompense supprimée.');
}
}
