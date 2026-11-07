<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Afficher la page de connexion
    public function showLogin()
    {
        return view('auth.login');
    }

    // Traiter la connexion
    public function login(Request $request)
    {
        // 1. Valider les champs du formulaire
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Vérifier les identifiants et l'état du compte
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
            'is_active' => true,
        ])) {

            // 3. Régénérer la session pour la sécurité
            $request->session()->regenerate();
            // 4. Rediriger après connexion
$user = Auth::user();

if ($user->role->name === 'admin') {
    return redirect()->route('dashboard');
}

if ($user->role->name === 'receptionniste') {
    return redirect()->route('receptionniste.dashboard');
}
if ($user->role->name === 'professionnel') {
    return redirect()->route('professionnel.dashboard');
}

return redirect('/');
    }
        // 5. Si les informations sont incorrectes
        return back()->withErrors([
            'email' => 'Identifiants incorrects ou compte désactivé.',
        ])->onlyInput('email');
    }
    public function destroy(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login.show');
}
}