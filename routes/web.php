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

    // Catalogue
    Route::get('/catalogue', [PrestationController::class, 'index'])
        ->middleware('role:admin,receptionniste,professionnel')
        ->name('catalogue.index');

    // Programme de fidélité
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

    // Tableau de bord administrateur
    Route::get('/dashboard', [DashboardController::class, 'admin'])
        ->middleware('admin')
        ->name('admin.dashboard');

    // Tableau de bord réceptionniste
    Route::get('/receptionniste/dashboard', function () {

        $user = auth()->user();
        $peutEncaisser = $user->isAdmin()
            || $user->hasPermission('paiements.enregistrer');

        $now = now();
        $debutJour = $now->copy()->startOfDay();
        $finJour = $now->copy()->endOfDay();

        $demainDebut = $now->copy()->addDay()->startOfDay();
        $demainFin = $now->copy()->addDay()->endOfDay();

        $rdvJour = \App\Models\RendezVous::whereBetween('debut', [$debutJour, $finJour])->get();

        $totalJour = $rdvJour->count();
        $termines = $rdvJour->where('statut', 'termine')->count();

        $restants = $rdvJour->where('fin', '>=', $now)
            ->whereNotIn('statut', ['termine', 'annule'])
            ->count();

        $enAttente = $rdvJour->where('statut', 'en_attente')->count();

        $clientsDuJour = \App\Models\Client::whereBetween('created_at', [$debutJour, $finJour])->count();

        $rdvDuJour = \App\Models\RendezVous::with([
            'client',
            'employe',
            'lignes.service',
            'lignes.forfait',
        ])
            ->whereBetween('debut', [$debutJour, $finJour])
            ->orderBy('debut', 'asc')
            ->get();

        $aEncaisser = \App\Models\RendezVous::with(['client'])
            ->whereBetween('debut', [$debutJour, $finJour])
            ->where('statut', '!=', 'annule')
            ->get();

        $rdvDemain = \App\Models\RendezVous::with([
            'client',
            'employe',
            'lignes.service',
            'lignes.forfait',
        ])
            ->whereBetween('debut', [$demainDebut, $demainFin])
            ->where('statut', '!=', 'annule')
            ->orderBy('debut', 'asc')
            ->get();

        $rappelsDemain = $rdvDemain->map(function ($rdv) {
            $tel = preg_replace('/\D+/', '', $rdv->client->telephone ?? '');

            if ($tel !== '' && strlen($tel) === 9) {
                $tel = '221' . $tel;
            }

            $message = rawurlencode(
                "Bonjour {$rdv->client->nom}, nous vous rappelons votre rendez-vous demain à "
                . \Carbon\Carbon::parse($rdv->debut)->format('H\hi') . '.'
            );

            return [
                'rdv'  => $rdv,
                'lien' => $tel !== '' ? "https://wa.me/{$tel}?text={$message}" : null,
            ];
        });

        $anniversaires = collect();

        if (\Illuminate\Support\Facades\Schema::hasColumn('clients', 'date_naissance')) {

            $clientsAnniv = \App\Models\Client::whereMonth('date_naissance', $now->month)
                ->whereDay('date_naissance', $now->day)
                ->get();

            $anniversaires = $clientsAnniv->map(function ($client) use ($now) {

                $age = $client->date_naissance
                    ? \Carbon\Carbon::parse($client->date_naissance)->age
                    : null;

                return [
                    'client' => $client,
                    'age' => $age,
                    'lien' => null,
                ];
            });
        }

        $minutes = function ($rdv) {

            if (isset($rdv->debut) && isset($rdv->fin)) {
                return \Carbon\Carbon::parse($rdv->debut)
                    ->diffInMinutes(\Carbon\Carbon::parse($rdv->fin));
            }

            return 30;
        };

        $queryPaiements = \App\Models\Paiement::whereBetween('created_at', [$debutJour, $finJour]);

        if (\Illuminate\Support\Facades\Schema::hasColumn('paiements', 'user_id')) {
            $queryPaiements->where('user_id', $user->id);
        } elseif (\Illuminate\Support\Facades\Schema::hasColumn('paiements', 'employe_id')) {
            $queryPaiements->where('employe_id', $user->id);
        }

        $paiementsJour = $queryPaiements->get();

        $mesEncaissements = $paiementsJour->sum('montant');
        $mesPaiements = $paiementsJour->count();

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

    })->middleware('role:admin,receptionniste')
        ->name('receptionniste.dashboard');

    // Tableau de bord professionnel
    Route::get('/professionnel/dashboard', [ProfessionnelController::class, 'dashboard'])
        ->middleware('role:professionnel,admin')
        ->name('professionnel.dashboard');

    // Planning professionnel
    Route::get('/professionnel/planning', [ProfessionnelController::class, 'planning'])
        ->middleware('role:professionnel,admin')
        ->name('professionnel.planning');

    // Détails rendez-vous professionnel
    Route::get('/professionnel/rendez-vous/{rendezVous}', [ProfessionnelController::class, 'show'])
        ->middleware('role:professionnel,admin')
        ->name('professionnel.rendez-vous.show');

    // Changement statut rendez-vous professionnel
    Route::patch('/professionnel/rendez-vous/{rendezVous}/statut', [ProfessionnelController::class, 'statut'])
        ->middleware('role:professionnel,admin')
        ->name('professionnel.rendez-vous.statut');

    // Gestion des clients
    Route::middleware('role:admin,receptionniste')->group(function () {

        Route::get('clients/export', [ClientController::class, 'export'])
            ->name('clients.export');

        Route::resource('clients', ClientController::class)
            ->except(['destroy']);

        Route::post('clients/{client}/archive', [ClientController::class, 'archive'])
            ->name('clients.archive');

        Route::post('clients/{client}/restore', [ClientController::class, 'restore'])
            ->name('clients.restore');
    });

    // Anniversaires
    Route::middleware('role:admin,receptionniste')->group(function () {

        Route::get('anniversaires', [AnniversaireController::class, 'index'])
            ->name('anniversaires.index');

        Route::post('anniversaires/offre', [AnniversaireController::class, 'enregistrerOffre'])
            ->name('anniversaires.offre');

        Route::post('anniversaires/{client}/traite', [AnniversaireController::class, 'traiter'])
            ->name('anniversaires.traiter');

        Route::delete('anniversaires/{client}/traite', [AnniversaireController::class, 'annuler'])
            ->name('anniversaires.annuler');
    });

    // Campagnes WhatsApp
    Route::middleware('admin')
        ->prefix('campagnes')
        ->name('campagnes.')
        ->group(function () {

            Route::get('/', [CampagneController::class, 'index'])->name('index');
            Route::post('/', [CampagneController::class, 'store'])->name('store');

            Route::post('/destinataires/{destinataire}/ouvrir', [CampagneController::class, 'ouvrir'])
                ->name('destinataires.ouvrir');

            Route::patch('/destinataires/{destinataire}/suivi', [CampagneController::class, 'suivi'])
                ->name('destinataires.suivi');

            Route::get('/{campagne}', [CampagneController::class, 'show'])->name('show');
        });

    // Gestion des employés
    Route::middleware('admin')->group(function () {
        Route::resource('employes', EmployeController::class)
            ->parameters(['employes' => 'employe'])
            ->except(['destroy']);
    });

    // Paramètres des modèles WhatsApp
    Route::middleware('admin')->group(function () {

        Route::get('parametres/messages', [ModeleMessageController::class, 'index'])
            ->name('parametres.messages');

        Route::put('parametres/messages/{modele}', [ModeleMessageController::class, 'update'])
            ->name('parametres.messages.update');
    });

    // Prestations et forfaits
    Route::middleware('admin')
        ->prefix('prestations')
        ->name('prestations.')
        ->group(function () {

            Route::get('/', [PrestationController::class, 'index'])->name('index');
            Route::post('/', [PrestationController::class, 'storePrestation'])->name('store');
            Route::put('/{id}', [PrestationController::class, 'updatePrestation'])->name('update');
            Route::patch('/{id}/statut', [PrestationController::class, 'changerStatutPrestation'])->name('statut');
            Route::delete('/{id}', [PrestationController::class, 'destroyPrestation'])->name('destroy');

            Route::post('/forfaits', [PrestationController::class, 'storeForfait'])->name('forfaits.store');
            Route::put('/forfaits/{id}', [PrestationController::class, 'updateForfait'])->name('forfaits.update');
            Route::patch('/forfaits/{id}/statut', [PrestationController::class, 'changerStatutForfait'])->name('forfaits.statut');
            Route::delete('/forfaits/{id}', [PrestationController::class, 'destroyForfait'])->name('forfaits.destroy');

            Route::post('/categories', [PrestationController::class, 'store'])->name('categories.store');
            Route::put('/categories/{id}', [PrestationController::class, 'update'])->name('categories.update');
            Route::delete('/categories/{id}', [PrestationController::class, 'destroy'])->name('categories.destroy');
        });

    // Gestion de la caisse
    Route::middleware('role:admin,receptionniste')
        ->prefix('caisse')
        ->name('caisse.')
        ->group(function () {

            Route::get('/', [CaisseController::class, 'index'])->name('index');
            Route::get('/impayes', [CaisseController::class, 'impayes'])->name('impayes');
            Route::get('/{paiement}/recu', [CaisseController::class, 'recu'])->name('recu');

            Route::middleware('permission:paiements.enregistrer')->group(function () {
                Route::get('/encaisser', [CaisseController::class, 'create'])->name('create');
                Route::post('/', [CaisseController::class, 'store'])->name('store');
            });

            Route::patch('/{paiement}/annuler', [CaisseController::class, 'annuler'])
                ->middleware('permission:paiements.annuler')
                ->name('annuler');
        });

    // Modes de paiement
    Route::middleware('admin')
        ->prefix('caisse/modes')
        ->name('caisse.modes.')
        ->group(function () {

            Route::get('/', [CaisseController::class, 'modes'])->name('index');
            Route::post('/', [CaisseController::class, 'storeMode'])->name('store');
            Route::patch('/{mode}', [CaisseController::class, 'toggleMode'])->name('toggle');
        });

    // Dépenses
    Route::middleware('admin')
        ->prefix('depenses')
        ->name('depenses.')
        ->group(function () {

            Route::get('/', [DepenseController::class, 'index'])->name('index');
            Route::get('/creer', [DepenseController::class, 'create'])->name('create');
            Route::post('/', [DepenseController::class, 'store'])->name('store');
            Route::patch('/{depense}/annuler', [DepenseController::class, 'annuler'])->name('annuler');

            Route::get('/{depense}/annuler', fn() => redirect()->route('depenses.index'));
        });

    // Rapports et statistiques
    Route::middleware('admin')
        ->prefix('rapports')
        ->name('rapports.')
        ->group(function () {

            Route::get('/', [RapportController::class, 'index'])->name('index');
            Route::get('/imprimer', [RapportController::class, 'imprimer'])->name('imprimer');
            Route::get('/export', [RapportController::class, 'exporter'])->name('export');
        });

    // Gestion du stock
    Route::middleware('admin')
        ->prefix('stock')
        ->name('stock.')
        ->group(function () {

            Route::get('/', [ProduitController::class, 'index'])->name('index');

            Route::get('/mouvements', [MouvementStockController::class, 'index'])
                ->name('mouvements.index');

            Route::get('/creer', [ProduitController::class, 'create'])
                ->name('produits.create');

            Route::post('/', [ProduitController::class, 'store'])
                ->name('produits.store');

            Route::get('/{produit}/modifier', [ProduitController::class, 'edit'])
                ->name('produits.edit');

            Route::put('/{produit}', [ProduitController::class, 'update'])
                ->name('produits.update');

            Route::get('/{produit}/entree', [MouvementStockController::class, 'create'])
                ->defaults('type', 'entree')
                ->name('entree.create');

            Route::post('/{produit}/entree', [MouvementStockController::class, 'store'])
                ->defaults('type', 'entree')
                ->name('entree.store');

            Route::get('/{produit}/sortie', [MouvementStockController::class, 'create'])
                ->defaults('type', 'sortie')
                ->name('sortie.create');

            Route::post('/{produit}/sortie', [MouvementStockController::class, 'store'])
                ->defaults('type', 'sortie')
                ->name('sortie.store');
        });

    // Déconnexion
    Route::post('/deconnexion', [AuthController::class, 'destroy'])
        ->name('logout');
});

// Gestion des rendez-vous
Route::middleware(['auth', 'role:admin,receptionniste'])
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
