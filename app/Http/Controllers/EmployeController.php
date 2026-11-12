<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Service;
use App\Models\Disponibilite;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Employe::class);

        $employes = Employe::with('services')
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        $voirPerformances = $request->user()->can('voirPerformances', Employe::class);
        $perfs = collect();
        [$du, $au] = $this->periode($request);

        if ($voirPerformances) {
            $perfs = $employes->mapWithKeys(
                fn (Employe $e) => [$e->id => $e->performances($du, $au)]
            );
        }

        return view('employes.index', compact('employes', 'perfs', 'voirPerformances', 'du', 'au'));
    }

    public function create()
    {
        $this->authorize('create', Employe::class);

        return view('employes.form', [
            'employe'  => new Employe(['is_active' => true]),
            'services' => Service::actifs()->orderBy('nom')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Employe::class);

        $employe = Employe::create($this->valider($request));
        $employe->services()->sync($request->input('services', []));
        $this->enregistrerHoraires($employe, $request);

        return redirect()->route('employes.show', $employe)->with('success', 'Employé créé.');
    }

    public function show(Request $request, Employe $employe)
    {
        $this->authorize('view', Employe::class);

        $employe->load(['services', 'disponibilites']);

        $perf = null;
        [$du, $au] = $this->periode($request);

        if ($request->user()->can('voirPerformances', Employe::class)) {
            $perf = $employe->performances($du, $au);
        }

        return view('employes.show', compact('employe', 'perf', 'du', 'au'));
    }

    public function edit(Employe $employe)
    {
        $this->authorize('update', Employe::class);

        return view('employes.form', [
            'employe'  => $employe->load('services'),
            'services' => Service::actifs()->orderBy('nom')->get(),
        ]);
    }

    public function update(Request $request, Employe $employe)
    {
        $this->authorize('update', Employe::class);

        $employe->update($this->valider($request));
        $employe->services()->sync($request->input('services', []));

        return redirect()->route('employes.show', $employe)->with('success', 'Fiche mise à jour.');
    }

    private function valider(Request $request): array
    {
        $data = $request->validate([
            'prenom'          => ['required', 'string', 'max:100'],
            'nom'             => ['required', 'string', 'max:100'],
            'telephone'       => ['nullable', 'string', 'max:30'],
            'fonction'        => ['required', 'string', 'max:100'],
            'specialites'     => ['nullable', 'string', 'max:500'],
            'taux_commission' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'services'        => ['array'],
            'services.*'      => ['integer', 'exists:services,id'],

            'horaires'           => ['nullable', 'array'],
'horaires.*.debut'   => ['nullable', 'date_format:H:i'],
'horaires.*.fin'     => ['nullable', 'date_format:H:i', 'after:horaires.*.debut'],
        ]);

        // Seul l'admin peut fixer la rémunération.
        if (! $request->user()->can('voirPerformances', Employe::class)) {
            unset($data['taux_commission']);
        } else {
            $data['taux_commission'] = $data['taux_commission'] ?? 0;
        }

        $data['is_active'] = $request->boolean('is_active');
        unset($data['services']);
        unset($data['horaires']);
        return $data;
    }
/** Remplace les horaires de l'employé par ceux du formulaire. */
private function enregistrerHoraires(Employe $employe, Request $request): void
{
    $employe->disponibilites()->delete();

    foreach (array_keys(Disponibilite::JOURS) as $jour) {
        if (! $request->boolean("horaires.$jour.actif")) {
            continue;
        }

        $debut = $request->input("horaires.$jour.debut");
        $fin   = $request->input("horaires.$jour.fin");

        if ($debut && $fin) {
            $employe->disponibilites()->create([
                'jour_semaine' => $jour,
                'heure_debut'  => $debut,
                'heure_fin'    => $fin,
            ]);
        }
    }
}
    private function periode(Request $request): array
    {
        $du = Carbon::parse($request->input('du', now()->startOfMonth()))->startOfDay();
        $au = Carbon::parse($request->input('au', now()->endOfMonth()))->endOfDay();

        return [$du, $au];
    }
}