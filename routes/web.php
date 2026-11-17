<?php
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendrierController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FideliteController;
use App\Http\Controllers\PrestationController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\AnniversaireController;
use App\Http\Controllers\ModeleMessageController;
use App\Http\Controllers\CampagneController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\MouvementStockController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\ProfessionnelController;
use Illuminate\Support\Facades\Route;

// Accueil
Route::get('/', function () {
    return redirect()->route('login.show');
});

// Routes pour les visiteurs non connectés
Route::middleware('guest')->group(function () {

    Route::get('/connexion', [AuthController::class, 'showLogin'])
        ->name('login.show');

    Route::post('/connexion', [AuthController::class, 'login'])
        ->name('login');

    // Mot de passe oublié
    Route::get('/mot-de-passe-oublie', [PasswordResetController::class, 'demande'])
        ->name('password.request');

    Route::post('/mot-de-passe-oublie', [PasswordResetController::class, 'envoyerLien'])
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::get('/reinitialiser-mot-de-passe/{token}', [PasswordResetController::class, 'formulaire'])
        ->name('password.reset');

    Route::post('/reinitialiser-mot-de-passe', [PasswordResetController::class, 'reinitialiser'])
        ->middleware('throttle:5,1')
        ->name('password.update');
});

// Routes utilisateurs connectés
Route::middleware('auth')->group(function () {
    // Route de secours pour le catalogue visible par la réception
    Route::get('/catalogue', [PrestationController::class, 'index'])
        ->name('catalogue.index');

    // Programme de fidélité : administrateur uniquement
    Route::middleware('admin')->group(function () {

        Route::get('/fidelite', [FideliteController::class, 'index'])
            ->name('fidelite.index');

        Route::post('/fidelite/regles', [FideliteController::class, 'storeRegle'])
            ->name('fidelite.regles.store');

        Route::put('/fidelite/regles/{regle}', [FideliteController::class, 'updateRegle'])
            ->name('fidelite.regles.update');

        Route::post('/fidelite/recompenses', [FideliteController::class, 'storeRecompense'])
            ->name('fidelite.recompenses.store');

        Route::delete('/fidelite/recompenses/{recompense}', [FideliteController::class, 'destroyRecompense'])
            ->name('fidelite.recompenses.destroy');
    });

    // Tableaux de bord
    Route::get('/dashboard', [DashboardController::class, 'admin'])
        ->name('admin.dashboard');

    Route::get('/receptionniste/dashboard', function () {
        $user = auth()->user();
        $peutEncaisser = in_array($user->role->name, ['admin', 'receptionniste']);

        $now = now();
        $debutJour = $now->copy()->startOfDay();
        $finJour = $now->copy()->endOfDay();

        $demainDebut = $now->copy()->addDay()->startOfDay();
        $demainFin = $now->copy()->addDay()->endOfDay();

        // Statistiques des rendez-vous du jour
        $rdvJour = \App\Models\RendezVous::whereBetween('debut', [$debutJour, $finJour])->get();

        $totalJour = $rdvJour->count();
        $termines = $rdvJour->where('statut', 'termine')->count();
        $restants = $rdvJour->where('fin', '>=', $now)
                            ->whereNotIn('statut', ['termine', 'annule'])
                            ->count();
        
        $enAttente = $rdvJour->where('statut', 'en_attente')->count();
        $clientsDuJour = \App\Models\Client::whereBetween('created_at', [$debutJour, $finJour])->count();

        // Liste des rendez-vous du jour pour le planning
        $rdvDuJour = \App\Models\RendezVous::with(['client', 'prestation', 'employe'])
            ->whereBetween('debut', [$debutJour, $finJour])
            ->orderBy('debut', 'asc')
            ->get();

        // Rendez-vous à encaisser
        $aEncaisser = \App\Models\RendezVous::with(['client'])
            ->whereBetween('debut', [$debutJour, $finJour])
            ->where('statut', '!=', 'annule')
            ->get();

        // Rappels WhatsApp pour les rendez-vous de demain
        $rdvDemain = \App\Models\RendezVous::with(['client', 'prestation', 'employe'])
            ->whereBetween('debut', [$demainDebut, $demainFin])
            ->where('statut', '!=', 'annule')
            ->orderBy('debut', 'asc')
            ->get();

        $rappelsDemain = $rdvDemain->map(function($rdv) {
            return ['rdv' => $rdv];
        });

        // Anniversaires du jour (basé sur la date de naissance des clients, si la colonne 'date_naissance' existe)
        $anniversaires = collect();
        if (\Illuminate\Support\Facades\Schema::hasColumn('clients', 'date_naissance')) {
            $clientsAnniv = \App\Models\Client::whereMonth('date_naissance', $now->month)
                ->whereDay('date_naissance', $now->day)
                ->get();

            $anniversaires = $clientsAnniv->map(function($client) use ($now) {
                $age = $client->date_naissance ? \Carbon\Carbon::parse($client->date_naissance)->age : null;
                return [
                    'client' => $client,
                    'age' => $age,
                    'lien' => null // ou un lien WhatsApp vers le client si tu en as un
                ];
            });
        }

        // Fonction pour calculer la durée en minutes d'un rendez-vous
        $minutes = function($rdv) {
            if (isset($rdv->debut) && isset($rdv->fin)) {
                return \Carbon\Carbon::parse($rdv->debut)->diffInMinutes(\Carbon\Carbon::parse($rdv->fin));
            }
            return 30;
        };

        // Encaissements du jour
        $queryPaiements = \App\Models\Paiement::whereBetween('created_at', [$debutJour, $finJour]);
        if (\Illuminate\Support\Facades\Schema::hasColumn('paiements', 'user_id')) {
            $queryPaiements->where('user_id', $user->id);
        } elseif (\Illuminate\Support\Facades\Schema::hasColumn('paiements', 'employe_id')) {
            $queryPaiements->where('employe_id', $user->id);
        }

        $paiementsJour = $queryPaiements->get();
        $mesEncaissements = $paiementsJour->sum('montant');
        $mesPaiements = $paiementsJour->count();

        // Fonction de formatage des montants
        $f = fn($val) => number_format($val, 0, ',', ' ');

        return view('receptionniste.dashboard', compact(
            'peutEncaisser',
            'totalJour',
            'termines',
            'restants',
            'enAttente',
            'clientsDuJour',
            'rdvDuJour',
            'aEncaisser',
            'rappelsDemain',
            'anniversaires',
            'minutes',
            'mesEncaissements',
            'mesPaiements',
            'f'
        ));
    })->name('receptionniste.dashboard');

    Route::get('/professionnel/dashboard', function () {
        return view('professionnel.dashboard');
    })->name('professionnel.dashboard');

    // Gestion des clients
    Route::get('clients/export', [ClientController::class, 'export'])
        ->name('clients.export');

    Route::resource('clients', ClientController::class)
        ->except(['destroy']);

    Route::post('clients/{client}/archive', [ClientController::class, 'archive'])
        ->name('clients.archive');

    Route::post('clients/{client}/restore', [ClientController::class, 'restore'])
        ->name('clients.restore');

    // Anniversaires
    Route::get('anniversaires', [AnniversaireController::class, 'index'])
        ->name('anniversaires.index');

    Route::post('anniversaires/offre', [AnniversaireController::class, 'enregistrerOffre'])
        ->name('anniversaires.offre');

    Route::post('anniversaires/{client}/traite', [AnniversaireController::class, 'traiter'])
        ->name('anniversaires.traiter');

    Route::delete('anniversaires/{client}/traite', [AnniversaireController::class, 'annuler'])
        ->name('anniversaires.annuler');

    // Campagnes WhatsApp : administrateur uniquement
    Route::middleware('admin')
        ->prefix('campagnes')
        ->name('campagnes.')
        ->group(function () {

            Route::get('/', [CampagneController::class, 'index'])
                ->name('index');

            Route::post('/', [CampagneController::class, 'store'])
                ->name('store');

            Route::post('/destinataires/{destinataire}/ouvrir', [CampagneController::class, 'ouvrir'])
                ->name('destinataires.ouvrir');

            Route::patch('/destinataires/{destinataire}/suivi', [CampagneController::class, 'suivi'])
                ->name('destinataires.suivi');

            Route::get('/{campagne}', [CampagneController::class, 'show'])
                ->name('show');
        });

    // Gestion des employés
    Route::resource('employes', EmployeController::class)
        ->parameters(['employes' => 'employe'])
        ->except(['destroy']);

    // Paramètres : modèles de messages WhatsApp
    Route::get('parametres/messages', [ModeleMessageController::class, 'index'])
        ->name('parametres.messages');

    Route::put('parametres/messages/{modele}', [ModeleMessageController::class, 'update'])
        ->name('parametres.messages.update');

    // Prestations et forfaits
    Route::middleware('admin')
        ->prefix('prestations')
        ->name('prestations.')
        ->group(function () {

            Route::get('/', [PrestationController::class, 'index'])
                ->name('index');

            Route::post('/', [PrestationController::class, 'storePrestation'])
                ->name('store');

            Route::put('/{id}', [PrestationController::class, 'updatePrestation'])
                ->name('update');

            Route::patch('/{id}/statut', [PrestationController::class, 'changerStatutPrestation'])
                ->name('statut');

            Route::delete('/{id}', [PrestationController::class, 'destroyPrestation'])
                ->name('destroy');

            Route::post('/forfaits', [PrestationController::class, 'storeForfait'])
                ->name('forfaits.store');

            Route::put('/forfaits/{id}', [PrestationController::class, 'updateForfait'])
                ->name('forfaits.update');

            Route::patch('/forfaits/{id}/statut', [PrestationController::class, 'changerStatutForfait'])
                ->name('forfaits.statut');

            Route::delete('/forfaits/{id}', [PrestationController::class, 'destroyForfait'])
                ->name('forfaits.destroy');

            Route::post('/categories', [PrestationController::class, 'store'])
                ->name('categories.store');

            Route::put('/categories/{id}', [PrestationController::class, 'update'])
                ->name('categories.update');

            Route::delete('/categories/{id}', [PrestationController::class, 'destroy'])
                ->name('categories.destroy');
        });

    // Gestion de la caisse
    Route::middleware('role:admin,receptionniste')
        ->prefix('caisse')
        ->name('caisse.')
        ->group(function () {

            Route::get('/', [CaisseController::class, 'index'])
                ->name('index');

            Route::get('/encaisser', [CaisseController::class, 'create'])
                ->name('create');

            Route::get('/impayes', [CaisseController::class, 'impayes'])
                ->name('impayes');

            Route::post('/', [CaisseController::class, 'store'])
                ->name('store');

            Route::get('/{paiement}/recu', [CaisseController::class, 'recu'])
                ->name('recu');

            Route::patch('/{paiement}/annuler', [CaisseController::class, 'annuler'])
                ->name('annuler');
        });

    // Modes de paiement configurables
    Route::middleware('admin')
        ->prefix('caisse/modes')
        ->name('caisse.modes.')
        ->group(function () {

            Route::get('/', [CaisseController::class, 'modes'])
                ->name('index');

            Route::post('/', [CaisseController::class, 'storeMode'])
                ->name('store');

            Route::patch('/{mode}', [CaisseController::class, 'toggleMode'])
                ->name('toggle');
        });

    // Dépenses
    Route::middleware('admin')
        ->prefix('depenses')
        ->name('depenses.')
        ->group(function () {

            Route::get('/', [DepenseController::class, 'index'])
                ->name('index');

            Route::get('/creer', [DepenseController::class, 'create'])
                ->name('create');

            Route::post('/', [DepenseController::class, 'store'])
                ->name('store');

            Route::patch('/{depense}/annuler', [DepenseController::class, 'annuler'])
                ->name('annuler');

            Route::get('/{depense}/annuler', fn() => redirect()->route('depenses.index'));
        });

    // Rapports et statistiques (admin)
    Route::middleware('admin')
        ->prefix('rapports')
        ->name('rapports.')
        ->group(function () {

            Route::get('/', [RapportController::class, 'index'])
                ->name('index');

            Route::get('/imprimer', [RapportController::class, 'imprimer'])
                ->name('imprimer');

            Route::get('/export', [RapportController::class, 'exporter'])
                ->name('export');
        });

    // Déconnexion
    Route::post('/deconnexion', [AuthController::class, 'destroy'])
        ->name('logout');
});


// Gestion des rendez-vous
Route::middleware(['auth', 'role:admin,receptionniste,professionnel'])
    ->prefix('rendez-vous')
    ->name('rendez-vous.')
    ->group(function () {

        Route::get('/', [CalendrierController::class, 'index'])
            ->name('index');

        Route::get('/creer', [RendezVousController::class, 'create'])
            ->name('create');

        Route::post('/', [RendezVousController::class, 'store'])
            ->name('store');

        Route::patch('/{rendezVous}/statut', [RendezVousController::class, 'changerStatut'])
            ->name('statut');

        Route::patch('/{rendezVous}/deplacer', [RendezVousController::class, 'deplacer'])
            ->name('deplacer');
    });
