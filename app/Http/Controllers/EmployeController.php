<?php

namespace App\Http\Controllers;

use App\Models\Employeuse;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;;

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
                fn(Employe $e) => [$e->id => $e->performances($du, $au)]
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

        $employe = DB::transaction(function () use ($request) {
            $employe = Employe::create($this->valider($request));
            $employe->services()->sync($request->input('services', []));
            $this->enregistrerHoraires($employe, $request);
            $this->creerCompte($employe, $request);

            return $employe;
        });

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

        DB::transaction(function () use ($request, $employe) {
            $employe->update($this->valider($request));
            $employe->services()->sync($request->input('services', []));

            // Les horaires ne sont remplacés que si le formulaire les envoie.
            if ($request->has('horaires')) {
                $this->enregistrerHoraires($employe, $request);
            }

            // Permet de relier un compte à une fiche créée sans compte.
            if (! $employe->user_id) {
                $this->creerCompte($employe, $request);
            }
        });

        return redirect()->route('employes.show', $employe)->with('success', 'Fiche mise à jour.');
    }

    private function valider(Request $request): array
    {
        $data = $request->validate([
            'prenom'          => ['required', 'string', 'max:100'],
            'nom'             => ['required', 'string', 'max:100'],
            'telephone'        => ['required', 'string', 'max:20'],
            'compte_email'     => ['nullable', 'email', 'max:255', 'unique:users,email', 'required_with:compte_password'],
            'compte_password'  => ['nullable', 'string', 'min:10', 'required_with:compte_email'],
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
        unset($data['compte_email'], $data['compte_password']);
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
    /** Crée le compte de connexion « professionnel » et le relie à la fiche. */
    private function creerCompte(Employe $employe, Request $request): void
    {
        if (! $request->filled('compte_email')) {
            return;
        }

        $user = User::create([
            'name'      => $employe->prenom . ' ' . $employe->nom,
            'email'     => $request->input('compte_email'),
            'password'  => Hash::make($request->input('compte_password')),
            'role_id'   => Role::query()->where('name', 'professionnel')->value('id'),
            'is_active' => true,
        ]);

        $employe->update(['user_id' => $user->id]);
    }
    private function periode(Request $request): array
    {
        $du = Carbon::parse($request->input('du', now()->startOfMonth()))->startOfDay();
        $au = Carbon::parse($request->input('au', now()->endOfMonth()))->endOfDay();

        return [$du, $au];
    }
}
