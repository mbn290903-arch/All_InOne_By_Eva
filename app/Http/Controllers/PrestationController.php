<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrestationController extends Controller
{
    private function formatDuree(int $minutes): string
    {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return $h === 0 ? "{$m} min" : ($m ? "{$h} h {$m}" : "{$h} h");
    }

    public function index(Request $request)
    {
        // ── Prestations (filtrées) ──
        $services = DB::table('services as s')
            ->join('categories_services as c', 'c.id', '=', 's.categorie_service_id')
            ->select('s.*', 'c.nom as categorie_nom')
            ->when($request->q, fn($q, $v) => $q->where('s.nom', 'like', "%{$v}%"))
            ->when($request->zone, fn($q, $v) => $q->where('s.zone', $v))
            ->when($request->statut, fn($q, $v) => $q->where('s.is_active', $v === 'active' ? 1 : 0))
            ->when($request->categorie, fn($q, $v) => $q->where('s.categorie_service_id', $v))
            ->orderBy('c.ordre')->orderBy('s.nom')
            ->limit($request->boolean('tout') ? 1000 : 10)
            ->get();

        // ── Employés par prestation ──
        $employes = DB::table('employe_service as es')
            ->join('employes as e', 'e.id', '=', 'es.employe_id')
            ->whereIn('es.service_id', $services->pluck('id'))
            ->select('es.service_id', 'e.prenom', 'e.nom')
            ->get()
            ->groupBy('service_id');

        $services->each(function ($s) use ($employes) {
            $s->duree_format = $this->formatDuree($s->duree_minutes);
            $s->prix_format  = number_format($s->prix, 0, ',', ' ') . ' FCFA';
            $s->public = str_contains(mb_strtolower($s->categorie_nom), 'enfant')
                ? 'Enfant'
                : ($s->zone === 'femmes' ? 'Femme' : 'Tous');
            $s->desactivee   = $s->is_active ? null : Carbon::parse($s->updated_at)->format('d/m/Y');
            $s->realisateurs = ($employes[$s->id] ?? collect())->map(
                fn($e) => mb_strtoupper(mb_substr($e->prenom, 0, 1) . mb_substr($e->nom, 0, 1))
            );
        });

        // ── Catégories avec nombre de prestations ──
        $categories = DB::table('categories_services as c')
            ->leftJoin('services as s', 's.categorie_service_id', '=', 'c.id')
            ->select('c.id', 'c.nom', 'c.ordre', DB::raw('COUNT(s.id) as services_count'))
            ->groupBy('c.id', 'c.nom', 'c.ordre')
            ->orderBy('c.ordre')
            ->get();

        // ── Forfaits et leur contenu ──
        $contenus = DB::table('forfait_service as fs')
            ->join('services as s', 's.id', '=', 'fs.service_id')
            ->select('fs.forfait_id', 'fs.service_id', 's.nom', 's.prix', 's.duree_minutes', 's.zone')
            ->get()
            ->groupBy('forfait_id');

        $forfaits = DB::table('forfaits')->get()->each(function ($f) use ($contenus) {
            $lignes = $contenus[$f->id] ?? collect();
            $f->lignes       = $lignes->pluck('nom');
            $f->service_ids = $lignes->pluck('service_id')->all();
            $f->prix_normal  = $lignes->sum('prix');
            $f->reduction    = $f->prix_normal > 0 ? (int) round((1 - $f->prix / $f->prix_normal) * 100) : 0;
            $f->zone         = $lignes->isNotEmpty() && $lignes->every(fn($l) => $l->zone === 'femmes') ? 'femmes' : 'mixte';
            $f->duree_format = $this->formatDuree((int) $lignes->sum('duree_minutes'));
        });
        $servicesForfaits = DB::table('services')
            ->orderBy('nom')
            ->get();
        return view('prestations.index', [
            'categories' => $categories,
            'groupes'    => $services->groupBy('categorie_nom'),
            'total'      => DB::table('services')->count(),
            'affichees'  => $services->count(),
            'forfaits'   => $forfaits,
            'servicesForfaits' => $servicesForfaits,
        ]);
    }
    public function store(Request $request)
    {
        // Validation du nom de la catégorie
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:categories_services,nom',
        ], [
            'nom.required' => 'Le nom de la catégorie est obligatoire.',
            'nom.unique' => 'Cette catégorie existe déjà.',
            'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',
        ]);

        // Déterminer automatiquement le prochain ordre
        $ordre = DB::table('categories_services')->max('ordre') ?? 0;

        // Enregistrer la catégorie
        DB::table('categories_services')->insert([
            'nom' => $validated['nom'],
            'ordre' => $ordre + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('prestations.index')
            ->with('success', 'Catégorie ajoutée avec succès.');
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:categories_services,nom,' . $id,
        ], [
            'nom.required' => 'Le nom de la catégorie est obligatoire.',
            'nom.unique' => 'Cette catégorie existe déjà.',
            'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',
        ]);

        DB::table('categories_services')
            ->where('id', $id)
            ->update([
                'nom' => $validated['nom'],
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('prestations.index')
            ->with('success', 'Catégorie modifiée avec succès.');
    }
    public function destroy($id)
    {
        $categorie = DB::table('categories_services')
            ->where('id', $id)
            ->first();

        if (!$categorie) {
            return redirect()
                ->route('prestations.index')
                ->with('error', 'Catégorie introuvable.');
        }

        $nombrePrestations = DB::table('services')
            ->where('categorie_service_id', $id)
            ->count();

        if ($nombrePrestations > 0) {
            return redirect()
                ->route('prestations.index')
                ->with('error', 'Impossible de supprimer cette catégorie : elle contient encore des prestations.');
        }

        DB::table('categories_services')
            ->where('id', $id)
            ->delete();

        return redirect()
            ->route('prestations.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
    public function storePrestation(Request $request)
    {
        $request->validate([
            'categorie_service_id' => 'required|exists:categories_services,id',
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|integer|min:0',
            'duree_minutes' => 'required|integer|min:1',
            'zone' => 'required|in:femmes,mixte',
        ]);

        DB::table('services')->insert([
            'categorie_service_id' => $request->categorie_service_id,
            'nom' => $request->nom,
            'description' => $request->description,
            'prix' => $request->prix,
            'duree_minutes' => $request->duree_minutes,
            'zone' => $request->zone,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('prestations.index')
            ->with('success', 'Prestation ajoutée avec succès.');
    }
    public function updatePrestation(Request $request, $id)
    {
        $request->validate([
            'categorie_service_id' => 'required|exists:categories_services,id',
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|integer|min:0',
            'duree_minutes' => 'required|integer|min:1',
            'zone' => 'required|in:femmes,mixte',
        ]);

        DB::table('services')
            ->where('id', $id)
            ->update([
                'categorie_service_id' => $request->categorie_service_id,
                'nom' => $request->nom,
                'description' => $request->description,
                'prix' => $request->prix,
                'duree_minutes' => $request->duree_minutes,
                'zone' => $request->zone,
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('prestations.index')
            ->with('success', 'Prestation modifiée avec succès.');
    }

    public function changerStatutPrestation($id)
    {
        $prestation = DB::table('services')
            ->where('id', $id)
            ->first();

        if (!$prestation) {
            return redirect()
                ->route('prestations.index')
                ->with('error', 'Prestation introuvable.');
        }

        DB::table('services')
            ->where('id', $id)
            ->update([
                'is_active' => !$prestation->is_active,
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('prestations.index')
            ->with('success', $prestation->is_active
                ? 'Prestation désactivée avec succès.'
                : 'Prestation activée avec succès.');
    }


    public function destroyPrestation($id)
    {
        $prestation = DB::table('services')
            ->where('id', $id)
            ->first();

        if (!$prestation) {
            return redirect()
                ->route('prestations.index')
                ->with('error', 'Prestation introuvable.');
        }

        $utiliseeDansRendezVous = DB::table('rendez_vous_service')
            ->where('service_id', $id)
            ->exists();

        $utiliseeDansForfait = DB::table('forfait_service')
            ->where('service_id', $id)
            ->exists();

        if ($utiliseeDansRendezVous || $utiliseeDansForfait) {
            return redirect()
                ->route('prestations.index')
                ->with('error', 'Impossible de supprimer cette prestation : elle est utilisée dans un rendez-vous ou un forfait.');
        }

        DB::transaction(function () use ($id) {
            DB::table('services')
                ->where('id', $id)
                ->delete();
        });

        return redirect()
            ->route('prestations.index')
            ->with('success', 'Prestation supprimée avec succès.');
    }

    public function storeForfait(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|integer|min:0',
            'services' => 'required|array|min:2',
            'services.*' => 'required|integer|distinct|exists:services,id',
        ], [
            'services.min' => 'Un forfait doit contenir au moins 2 prestations.',
        ]);

        DB::transaction(function () use ($request) {

            $forfaitId = DB::table('forfaits')->insertGetId([
                'nom' => $request->nom,
                'description' => $request->description,
                'prix' => $request->prix,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($request->services as $serviceId) {
                DB::table('forfait_service')->insert([
                    'forfait_id' => $forfaitId,
                    'service_id' => $serviceId,
                ]);
            }
        });

        return redirect()
            ->route('prestations.index')
            ->with('success', 'Forfait créé avec succès.');
    }

    public function updateForfait(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|integer|min:0',
            'services' => 'required|array|min:2',
            'services.*' => 'required|integer|distinct|exists:services,id',
        ], [
            'services.min' => 'Un forfait doit contenir au moins 2 prestations.',
        ]);

        DB::transaction(function () use ($request, $id) {

            DB::table('forfaits')
                ->where('id', $id)
                ->update([
                    'nom' => $request->nom,
                    'description' => $request->description,
                    'prix' => $request->prix,
                    'updated_at' => now(),
                ]);

            DB::table('forfait_service')
                ->where('forfait_id', $id)
                ->delete();

            foreach ($request->services as $serviceId) {
                DB::table('forfait_service')->insert([
                    'forfait_id' => $id,
                    'service_id' => $serviceId,
                ]);
            }
        });

        return redirect()
            ->route('prestations.index')
            ->with('success', 'Forfait modifié avec succès.');
    }

    public function changerStatutForfait($id)
    {
        $forfait = DB::table('forfaits')->where('id', $id)->first();

        if (!$forfait) {
            return redirect()->route('prestations.index')
                ->with('error', 'Forfait introuvable.');
        }

        DB::table('forfaits')
            ->where('id', $id)
            ->update([
                'is_active' => !$forfait->is_active,
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('prestations.index')
            ->with('success', 'Statut du forfait modifié.');
    }

    public function destroyForfait($id)
    {
        $forfait = DB::table('forfaits')->where('id', $id)->first();

        if (!$forfait) {
            return redirect()->route('prestations.index')
                ->with('error', 'Forfait introuvable.');
        }

        $utilise = DB::table('rendez_vous_service')
            ->where('forfait_id', $id)
            ->exists();

        if ($utilise) {
            return redirect()
                ->route('prestations.index')
                ->with('error', 'Impossible de supprimer ce forfait : il est utilisé dans un rendez-vous.');
        }

        DB::transaction(function () use ($id) {
            DB::table('forfait_service')
                ->where('forfait_id', $id)
                ->delete();

            DB::table('forfaits')
                ->where('id', $id)
                ->delete();
        });

        return redirect()
            ->route('prestations.index')
            ->with('success', 'Forfait supprimé avec succès.');
    }
}
