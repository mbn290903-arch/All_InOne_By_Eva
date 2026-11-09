<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendrierController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RendezVousController;
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

    // Dashboards
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');

    Route::get('/receptionniste/dashboard', function () {
        return view('receptionniste.dashboard');
    })->name('receptionniste.dashboard');

    Route::get('/professionnel/dashboard', function () {
        return view('professionnel.dashboard');
    })->name('professionnel.dashboard');

    // Gestion des clients
    Route::get('clients/export', [ClientController::class, 'export'])->name('clients.export');
    Route::resource('clients', ClientController::class)->except(['destroy']);
    Route::post('clients/{client}/archive', [ClientController::class, 'archive'])->name('clients.archive');
    Route::post('clients/{client}/restore', [ClientController::class, 'restore'])->name('clients.restore');

    // Déconnexion
    Route::post('/deconnexion', [AuthController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth'])->prefix('rendez-vous')->name('rendez-vous.')->group(function () {
    Route::get('/', [CalendrierController::class, 'index'])->name('index');
    Route::get('/creer', [RendezVousController::class, 'create'])->name('create');
    Route::post('/', [RendezVousController::class, 'store'])->name('store');
    Route::patch('/{rendezVous}/statut', [RendezVousController::class, 'changerStatut'])->name('statut');
    Route::patch('/{rendezVous}/deplacer', [RendezVousController::class, 'deplacer'])->name('deplacer');
});