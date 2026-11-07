<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendrierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login.show');
});

// Routes pour les visiteurs non connectés
Route::middleware('guest')->group(function () {

    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login.show');

    Route::post('/connexion', [AuthController::class, 'login'])->name('login');
});

// Routes pour les utilisateurs connectés
Route::middleware('auth')->group(function () {

    // Dashboard administrateur
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    // Dashboard réceptionniste
    Route::get('/receptionniste/dashboard', function () {
        return view('receptionniste.dashboard');
    })->name('receptionniste.dashboard');

    // Dashboard professionnel
    Route::get('/professionnel/dashboard', function () {
        return view('professionnel.dashboard');
    })->name('professionnel.dashboard');

    // Déconnexion
    Route::post('/deconnexion', [AuthController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth'])->prefix('rendez-vous')->name('rendez-vous.')->group(function () {

    // Afficher le calendrier
    Route::get('/', [CalendrierController::class, 'index'])->name('index');

    // Afficher le formulaire d'ajout
    Route::get('/creer', [RendezVousController::class, 'create'])->name('create');

    // Enregistrer le rendez-vous
    Route::post('/', [RendezVousController::class, 'store'])->name('store');
    // Changer le statut d'un rendez-vous
    Route::patch('/{rendezVous}/statut', [RendezVousController::class, 'changerStatut'])->name('statut');
    // Déplacer un rendez-vous
    Route::patch('/{rendezVous}/deplacer', [RendezVousController::class, 'deplacer'])->name('deplacer');
});
