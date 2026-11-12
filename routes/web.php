<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendrierController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PrestationController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\AnniversaireController;
use App\Http\Controllers\ModeleMessageController;
use App\Http\Controllers\CampagneController;
use App\Http\Controllers\EmployeController;
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


    // Anniversaires
    Route::get('anniversaires', [AnniversaireController::class, 'index'])
        ->name('anniversaires.index');

    Route::post('anniversaires/offre', [AnniversaireController::class, 'enregistrerOffre'])
        ->name('anniversaires.offre');

    Route::post('anniversaires/{client}/traite', [AnniversaireController::class, 'traiter'])
        ->name('anniversaires.traiter');

    Route::delete('anniversaires/{client}/traite', [AnniversaireController::class, 'annuler'])
        ->name('anniversaires.annuler');


    // Campagnes WhatsApp : accès administrateur uniquement
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


    // Gestion de la caisse (admin et réceptionniste)
    Route::middleware('role:admin,receptionniste')
        ->prefix('caisse')
        ->name('caisse.')
        ->group(function () {

            Route::get('/', [CaisseController::class, 'index'])
                ->name('index');

            Route::get('/encaisser', [CaisseController::class, 'create'])
                ->name('create');

            Route::post('/', [CaisseController::class, 'store'])
                ->name('store');

            Route::patch('/{paiement}/annuler', [CaisseController::class, 'annuler'])
                ->name('annuler');
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
