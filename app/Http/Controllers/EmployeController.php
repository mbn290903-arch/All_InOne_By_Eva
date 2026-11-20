<?php
namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Role;
use App\Models\User;
use App\Models\Service;
use App\Models\Disponibilite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class EmployeController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Employe::class);

        $employes = Employe::with('services')
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        $voirPerformances = $request->user()->can(
            'voirPerformances',
            Employe::class
        );

        $perfs = collect();

        [$du, $au] = $this->periode($request);

        if ($voirPerformances) {
            $perfs = $employes->mapWithKeys(
                fn (Employe $e) => [
                    $e->id => $e->performances($du, $au)
                ]
            );
        }

        return view('employes.index', compact(
            'employes',
            'perfs',
            'voirPerformances',
            'du',
            'au'
        ));
    }

    public function create()
    {
        $this->authorize('create', Employe::class);

        return view('employes.form', [
            'employe' => new Employe(['is_active' => true]),
            'services' => Service::actifs()->orderBy('nom')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Employe::class);

        $employe = DB::transaction(function () use ($request) {
            $data = $this->valider($request);

            $employe = Employe::create($data);

            $employe->services()->sync(
                $request->input('services', [])
            );

            $this->enregistrerHoraires($employe, $request);

            $this->creerCompte($employe, $request);

            return $employe;
        });

        return redirect()
            ->route('employes.show', $employe)
            ->with('success', 'Employé créé.');
    }

    public function show(Request $request, Employe $employe)
    {
        $this->authorize('view', $employe);

        $employe->load([
            'services',
            'disponibilites',
        ]);

        $perf = null;

        [$du, $au] = $this->periode($request);

        if ($request->user()->can(
            'voirPerformances',
            Employe::class
        )) {
            $perf = $employe->performances($du, $au);
        }

        return view('employes.show', compact(
            'employe',
            'perf',
            'du',
            'au'
        ));
    }

    public function edit(Employe $employe)
    {
        $this->authorize('update', $employe);

        return view('employes.form', [
            'employe' => $employe->load('services'),
            'services' => Service::actifs()->orderBy('nom')->get(),
        ]);
    }

    public function update(Request $request, Employe $employe)
    {
        $this->authorize('update', $employe);

        DB::transaction(function () use ($request, $employe) {
            $data = $this->valider($request);

            $employe->update($data);

            $employe->services()->sync(
                $request->input('services', [])
            );

            if ($request->has('horaires')) {
                $this->enregistrerHoraires($employe, $request);
            }

            if (! $employe->user_id) {
                $this->creerCompte($employe, $request);
            }
        });

        return redirect()
            ->route('employes.show', $employe)
            ->with('success', 'Fiche mise à jour.');
    }

    private function valider(Request $request): array
    {
        $data = $request->validate([
            'prenom' => ['required', 'string', 'max:100'],
            'nom' => ['required', 'string', 'max:100'],
            'telephone' => ['required', 'string', 'max:20'],

            'compte_email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                'required_with:compte_password',
            ],

            'compte_password' => [
                'nullable',
                'string',
                'min:12',
                'required_with:compte_email',
            ],

            'fonction' => ['required', 'string', 'max:100'],
            'specialites' => ['nullable', 'string', 'max:500'],

            'taux_commission' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'is_active' => ['sometimes', 'boolean'],

            'services' => ['sometimes', 'array'],
            'services.*' => [
                'integer',
                'distinct',
                Rule::exists('services', 'id')
                    ->where('is_active', true),
            ],

            'horaires' => ['sometimes', 'array'],
            'horaires.*.actif' => ['sometimes', 'boolean'],
            'horaires.*.debut' => [
                'nullable',
                'date_format:H:i',
            ],
            'horaires.*.fin' => [
                'nullable',
                'date_format:H:i',
                'after:horaires.*.debut',
            ],
        ]);

        $user = $request->user();

        // Seul l'administrateur peut modifier le statut actif.
        if (! $user->isAdmin()) {
            unset($data['is_active']);
            unset($data['taux_commission']);
        } else {
            $data['is_active'] = $request->boolean('is_active');
            $data['taux_commission'] =
                $data['taux_commission'] ?? 0;
        }

        unset(
            $data['services'],
            $data['horaires'],
            $data['compte_email'],
            $data['compte_password']
        );

        return $data;
    }

    /**
     * Remplace les horaires de l'employé.
     */
    private function enregistrerHoraires(
        Employe $employe,
        Request $request
    ): void {
        $employe->disponibilites()->delete();

        foreach (array_keys(Disponibilite::JOURS) as $jour) {
            if (! $request->boolean("horaires.$jour.actif")) {
                continue;
            }

            $debut = $request->input("horaires.$jour.debut");
            $fin = $request->input("horaires.$jour.fin");

            if ($debut && $fin) {
                $employe->disponibilites()->create([
                    'jour_semaine' => $jour,
                    'heure_debut' => $debut,
                    'heure_fin' => $fin,
                ]);
            }
        }
    }

    /**
     * Crée un compte professionnel et le relie à sa fiche.
     */
    private function creerCompte(
        Employe $employe,
        Request $request
    ): void {
        if (! $request->filled('compte_email')) {
            return;
        }

        // Vérifie que la fiche n'est pas déjà reliée à un compte.
        if ($employe->user_id) {
            return;
        }

        $roleId = Role::query()
            ->where('name', 'professionnel')
            ->value('id');

        if (! $roleId) {
            throw new \RuntimeException(
                "Le rôle professionnel n'existe pas."
            );
        }

        $user = User::create([
            'name' => $employe->prenom . ' ' . $employe->nom,
            'email' => $request->input('compte_email'),
            'password' => Hash::make(
                $request->input('compte_password')
            ),
            'role_id' => $roleId,
            'is_active' => true,
        ]);

        $employe->update([
            'user_id' => $user->id,
        ]);
    }

    private function periode(Request $request): array
    {
        $du = Carbon::parse(
            $request->input('du', now()->startOfMonth())
        )->startOfDay();

        $au = Carbon::parse(
            $request->input('au', now()->endOfMonth())
        )->endOfDay();

        if ($du->gt($au)) {
            abort(422, 'La date de début doit précéder la date de fin.');
        }

        return [$du, $au];
    }
}
