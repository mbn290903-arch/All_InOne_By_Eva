<?php

use App\Http\Controllers\DepenseController;
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendrierController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PrestationController;
use App\Http\Controllers\RendezVousController;
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
});

// Routes pour les utilisateurs connectés
Route::middleware('auth')->group(function () {

    // Tableaux de bord
    Route::get('/dashboard', [DashboardController::class, 'admin'])
        ->name('admin.dashboard');

    Route::get('/receptionniste/dashboard', function () {
        return view('receptionniste.dashboard');
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


    // Gestion de la caisse (admin et réceptionniste uniquement)
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

    // Modes de paiement configurables (admin)
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


    // Dépenses (admin)
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
            // Si quelqu'un ouvre l'adresse d'annulation en GET, on le renvoie à la liste
            Route::get('/{depense}/annuler', fn() => redirect()->route('depenses.index'));
        });


    // Déconnexion
    Route::post('/deconnexion', [AuthController::class, 'destroy'])
        ->name('logout');
});


// Gestion des rendez-vous
Route::middleware('auth')
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
