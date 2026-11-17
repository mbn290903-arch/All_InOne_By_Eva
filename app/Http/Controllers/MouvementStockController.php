<?php

namespace App\Http\Controllers;

use App\Models\MouvementStock;
use App\Models\Produit;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MouvementStockController extends Controller
{
    private function autoriser(string $type): void
    {
        $groupe = $type === 'entree' ? 'roles_gestion' : 'roles_sortie';
        abort_unless(in_array(auth()->user()->role->name, config("stock.$groupe")), 403);
    }

    public function index(Request $request)
    {
        abort_unless(in_array(auth()->user()->role->name, config('stock.roles_lecture')), 403);

        $mouvements = MouvementStock::with(['produit', 'enregistreur'])
            ->when($request->filled('produit_id'), fn ($q) => $q->where('produit_id', $request->produit_id))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('du'), fn ($q) => $q->whereDate('created_at', '>=', $request->du))
            ->when($request->filled('au'), fn ($q) => $q->whereDate('created_at', '<=', $request->au))
            ->latest('created_at')->latest('id')
            ->paginate(20)->withQueryString();

        return view('stock.mouvements', [
            'mouvements' => $mouvements,
            'produits'   => Produit::orderBy('nom')->get(['id', 'nom']),
        ]);
    }

    public function create(Produit $produit, string $type)
    {
        $this->autoriser($type);

        return view('stock.mouvement', compact('produit', 'type'));
    }

    public function store(Request $request, Produit $produit, string $type, StockService $stock)
    {
        $this->autoriser($type);

        $data = $request->validate([
            'quantite'    => ['required', 'numeric', 'gt:0'],
            'motif'       => ['required', Rule::in(array_keys(MouvementStock::MOTIFS[$type]))],
            'prix_achat'  => ['nullable', 'integer', 'min:0'],
            'fournisseur' => ['nullable', 'string', 'max:255'],
            // Une perte, une casse ou une correction doit être justifiée.
            'note'        => [Rule::requiredIf(in_array($request->motif, ['perte', 'casse', 'correction'])), 'nullable', 'string', 'max:200'],
        ]);

        // La table n'a qu'une colonne « motif » : on y met le libellé + la note.
        $motif = MouvementStock::MOTIFS[$type][$data['motif']];
        if (! empty($data['note'])) {
            $motif .= ' : ' . $data['note'];
        }

        $stock->enregistrer($produit, $type, (float) $data['quantite'], $motif, $request->user(), [
            'approvisionnement' => $type === 'entree' && $data['motif'] === 'approvisionnement',
            'prix_achat'        => $data['prix_achat'] ?? null,
            'fournisseur'       => $data['fournisseur'] ?? null,
        ]);

        return redirect()->route('stock.index')
            ->with('success', $type === 'entree' ? 'Approvisionnement enregistré.' : 'Sortie enregistrée.');
    }
}