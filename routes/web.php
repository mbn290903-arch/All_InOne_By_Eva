
<?php

use App\Http\Controllers\AuthController;
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

    // Dashboard administrateur
    Route::get('/dashboard', fn () => view('dashboard'))
        ->name('dashboard');

    // Dashboard réceptionniste
    Route::get('/receptionniste/dashboard', function () {
        return view('receptionniste.dashboard');
    })->name('receptionniste.dashboard');
// Dashboard professionnel
Route::get('/professionnel/dashboard', function () {
    return view('professionnel.dashboard');
})->name('professionnel.dashboard');
    // Déconnexion
    Route::post('/deconnexion', [AuthController::class, 'destroy'])
        ->name('logout');

});
