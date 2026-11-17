<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduitController extends Controller
{
    private function autoriser(string $groupe): void
    {
        abort_unless(in_array(auth()->user()->role->name, config("stock.$groupe")), 403);
    }

    public function index(Request $request)
    {
        $this->autoriser('roles_lecture');

        $query = Produit::query();

        if ($q = trim((string) $request->input('q'))) {
            $query->where(fn ($w) => $w->where('nom', 'like', "%$q%")->orWhere('fournisseur', 'like', "%$q%"));
        }
        if ($cat = $request->input('categorie')) {
            $query->where('categorie', $cat);
        }
        match ($request->input('statut')) {
            'faible'         => $query->stocksFaibles(),
            'presque_epuise' => $query->presqueEpuises(),
            'epuise'         => $query->epuises(),
            default          => null,
        };

        return view('stock.index', [
            'produits'   => $query->orderBy('nom')->paginate(15)->withQueryString(),
            'categories' => Produit::whereNotNull('categorie')->distinct()->orderBy('categorie')->pluck('categorie'),
            'stats' => [
                'total'          => Produit::count(),
                'faibles'        => Produit::stocksFaibles()->count(),
                'presque_epuise' => Produit::presqueEpuises()->count(),
                'epuise'         => Produit::epuises()->count(),
            ],
        ]);
    }

    public function create()
    {
        $this->autoriser('roles_gestion');

        return view('stock.form', [
            'produit'    => new Produit(['date_entree' => now()]),
            'categories' => Produit::whereNotNull('categorie')->distinct()->orderBy('categorie')->pluck('categorie'),
        ]);
    }

    public function store(Request $request, StockService $stock)
    {
        $this->autoriser('roles_gestion');

        $data = $request->validate($this->regles(true));
        $quantiteInitiale = (float) ($data['quantite'] ?? 0);
        unset($data['quantite']);

        DB::transaction(function () use ($data, $quantiteInitiale, $stock) {
            $produit = Produit::create($data + ['quantite' => 0]);

            // Le stock de départ est lui aussi un mouvement tracé.
            if ($quantiteInitiale > 0) {
                $stock->enregistrer($produit, 'entree', $quantiteInitiale, 'Stock initial', auth()->user());
            }
        });

        return redirect()->route('stock.index')->with('success', 'Produit ajouté.');
    }

    public function edit(Produit $produit)
    {
        $this->autoriser('roles_gestion');

        return view('stock.form', [
            'produit'    => $produit,
            'categories' => Produit::whereNotNull('categorie')->distinct()->orderBy('categorie')->pluck('categorie'),
        ]);
    }

    public function update(Request $request, Produit $produit)
    {
        $this->autoriser('roles_gestion');

        // La quantité n'est PAS modifiable ici : uniquement via entrée / sortie.
        $produit->update($request->validate($this->regles(false)));

        return redirect()->route('stock.index')->with('success', 'Produit mis à jour.');
    }

    private function regles(bool $creation): array
    {
        return [
            'nom'          => ['required', 'string', 'max:255'],
            'categorie'    => ['required', 'string', 'max:255'],
            'unite'        => ['required', 'string', 'max:20'],
            'prix_achat'   => ['required', 'integer', 'min:0'],
            'prix_vente'   => ['nullable', 'integer', 'min:0'],
            'seuil_alerte' => ['required', 'numeric', 'min:0'],
            'fournisseur'  => ['nullable', 'string', 'max:255'],
            'date_entree'  => ['required', 'date'],
        ] + ($creation ? ['quantite' => ['required', 'numeric', 'min:0']] : []);
    }
}