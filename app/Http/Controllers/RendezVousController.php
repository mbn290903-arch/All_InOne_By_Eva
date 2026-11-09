<?php

namespace App\Http\Controllers;

use App\Enums\ZoneSalon;
use Carbon\CarbonImmutable;
use App\Enums\StatutRendezVous;
use App\Models\RendezVous;
use Illuminate\Validation\Rule;
use App\Exceptions\RegleMetierException;
use App\Models\Client;
use App\Models\Employe;
use App\Models\Service;
use App\Models\Forfait;
use App\Services\RendezVousService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class RendezVousController extends Controller
{
    /**
     * Injection du service de gestion des rendez-vous.
     */
    public function __construct(
        private readonly RendezVousService $rendezVousService
    ) {}

    /**
     * Afficher le formulaire de création.
     */
    /**
     * Afficher le formulaire de création.
     */
    public function create()
    {
        // Récupérer les clients non archivés.
        $clients = Client::nonArchives()
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        // Récupérer les employés actifs.
        $employes = Employe::actifs()
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        // Récupérer les services actifs.
        $services = Service::actifs()
            ->orderBy('nom')
            ->get();

        // Récupérer les forfaits actifs avec leurs services.
        $forfaits = Forfait::actifs()
            ->with('services')
            ->orderBy('nom')
            ->get();

        // Zones du salon (zone mixte, espace femmes).
        $zones = ZoneSalon::cases();

        return view('rendez-vous.create', compact(
            'clients',
            'employes',
            'services',
            'forfaits',
            'zones'
        ));
    }

    /**
     * Enregistrer un nouveau rendez-vous.
     */
    public function store(Request $request)
    {
        // Règle de la date : pas dans le passé, sauf permission particulière.
        $reglesDebut = ['required', 'date'];

        if (! $request->user()->peutPlanifierDansLePasse()) {
            $reglesDebut[] = 'after_or_equal:now';
        }
        // 1. Valider les données reçues du formulaire.
        $data = $request->validate([

            'client_id' => [
                'required',
                'integer',
                'exists:clients,id'
            ],

            'employe_id' => [
                'required',
                'integer',
                'exists:employes,id'
            ],
            'zone' => [
                'required',
                Rule::enum(ZoneSalon::class)
            ],

            'debut' => $reglesDebut,

            'service_ids' => [
                'required_without:forfait_ids',
                'array',
                'min:1'
            ],

            'service_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:services,id'
            ],

            'forfait_ids' => [
                'required_without:service_ids',
                'array',
                'min:1'
            ],

            'forfait_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:forfaits,id'
            ],

            'commentaire' => [
                'nullable',
                'string',
                'max:2000'
            ],
        ]);

        // 2. Vérifier que le client n'est pas archivé.
        $clientExiste = Client::nonArchives()
            ->whereKey($data['client_id'])
            ->exists();

        if (! $clientExiste) {
            throw ValidationException::withMessages([
                'client_id' => 'Ce client est introuvable ou archivé.',
            ]);
        }

        // 3. Convertir la date et l'heure reçues du formulaire.
        $debut = Carbon::parse($data['debut']);

        // 4. Préparer les données attendues par le service.
        $donneesService = [
            'client_id' => (int) $data['client_id'],

            'employe_id' => (int) $data['employe_id'],

            'zone' => $data['zone'],

            'date' => $debut->toDateString(),

            'heure_debut' => $debut->format('H:i:s'),

            'services' => $data['service_ids'] ?? [],

            'forfaits' => $data['forfait_ids'] ?? [],

            'commentaire' => $data['commentaire'] ?? null,
        ];

        // 5. Appeler le service pour créer le rendez-vous.
        try {

            $this->rendezVousService->creer(
                $donneesService,
                $request->user()
            );
        } catch (RegleMetierException $exception) {

            // Adapter le nom des champs du service
            // aux noms utilisés dans le formulaire Blade.
            $champ = match ($exception->champ) {
                'services', 'zone' => 'service_ids',
                'forfaits' => 'forfait_ids',
                'heure_debut' => 'debut',
                'general' => 'debut',
                default => $exception->champ,
            };

            throw ValidationException::withMessages([
                $champ => $exception->getMessage(),
            ]);
        }

        // 6. Retourner au calendrier.
        return redirect()
            ->route('rendez-vous.index')
            ->with('success', 'Rendez-vous enregistré avec succès.');
    }
    /**
     * Changer le statut d'un rendez-vous.
     */
    public function changerStatut(Request $request, RendezVous $rendezVous)
    {
        $user = $request->user();

        // Un professionnel ne modifie que ses propres rendez-vous.
        if (! $user->isAdmin() && $user->employe && $user->employe->id !== $rendezVous->employe_id) {
            abort(403);
        }

        $data = $request->validate([
            'statut' => ['required', Rule::enum(StatutRendezVous::class)],
        ]);

        $nouveau = StatutRendezVous::from($data['statut']);

        if (! $rendezVous->statut->peutPasserA($nouveau)) {
            return back()->withErrors([
                'statut' => "Passage impossible de « {$rendezVous->statut->label()} » à « {$nouveau->label()} ».",
            ]);
        }

        try {
            $this->rendezVousService->changerStatut($rendezVous, $nouveau);
        } catch (RegleMetierException $exception) {
            return back()->withErrors(['statut' => $exception->getMessage()]);
        }

        return back()->with('success', 'Statut mis à jour : ' . $nouveau->label() . '.');
    }
    /**
     * Déplacer un rendez-vous.
     */
    public function deplacer(Request $request, RendezVous $rendezVous)
    {
        $user = $request->user();

        if (! $user->isAdmin() && $user->employe && $user->employe->id !== $rendezVous->employe_id) {
            abort(403);
        }

                $reglesDebut = ['required', 'date'];

        if (! $user->peutPlanifierDansLePasse()) {
            $reglesDebut[] = 'after_or_equal:now';
        }

        $data = $request->validate([
            'debut' => $reglesDebut,
        ]);

        try {
            $this->rendezVousService->deplacer(
                $rendezVous,
                CarbonImmutable::parse($data['debut'])
            );
        } catch (RegleMetierException $exception) {
            return back()->withErrors(['debut' => $exception->getMessage()]);
        }

        return back()->with('success', 'Rendez-vous déplacé.');
    }
}
