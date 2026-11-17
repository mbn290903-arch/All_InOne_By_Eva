<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class CatalogueController extends Controller
{
    /** Consultation seule des prestations et des tarifs. */
    public function index()
    {
        $services = DB::table('services as s')
            ->join('categories_services as c', 'c.id', '=', 's.categorie_service_id')
            ->where('s.is_active', true)
            ->orderBy('c.ordre')
            ->orderBy('c.nom')
            ->orderBy('s.nom')
            ->get(['c.nom as categorie', 's.nom', 's.description', 's.prix', 's.duree_minutes', 's.zone'])
            ->groupBy('categorie');

        $forfaits = DB::table('forfaits')
            ->where('is_active', true)
            ->orderBy('nom')
            ->get();

        $servicesDesForfaits = DB::table('forfait_service as fs')
            ->join('services as s', 's.id', '=', 'fs.service_id')
            ->get(['fs.forfait_id', 's.nom'])
            ->groupBy('forfait_id');

        return view('catalogue.index', compact('services', 'forfaits', 'servicesDesForfaits'));
    }
}