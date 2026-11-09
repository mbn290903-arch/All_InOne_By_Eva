
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PrestationController;
use Illuminate\Support\Facades\Route;

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

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/receptionniste/dashboard', function () {
        return view('receptionniste.dashboard');
    })->name('receptionniste.dashboard');

    Route::get('/professionnel/dashboard', function () {
        return view('professionnel.dashboard');
    })->name('professionnel.dashboard');

    Route::middleware('admin')->prefix('prestations')->name('prestations.')->group(function () {

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

    Route::post('/deconnexion', [AuthController::class, 'destroy'])
        ->name('logout');
});
