<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Affiche la page de connexion.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Authentifie un utilisateur actif.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Seuls les comptes actifs peuvent se connecter.
        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'is_active' => true,
        ])) {
            return back()
                ->withErrors([
                    'email' => 'Identifiants incorrects ou compte désactivé.',
                ])
                ->onlyInput('email');
        }

        // Protection contre la fixation de session.
        $request->session()->regenerate();

        $user = Auth::user();

        // Vérifie que le compte possède un rôle valide.
        $role = $user?->role?->name;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'receptionniste' => redirect()->route('receptionniste.dashboard'),
            'professionnel' => redirect()->route('professionnel.dashboard'),

            default => $this->refuserAcces($request),
        };
    }

    /**
     * Déconnecte l'utilisateur et détruit sa session.
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.show');
    }

    /**
     * Déconnecte un compte dont le rôle est invalide.
     */
    private function refuserAcces(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login.show')
            ->withErrors([
                'email' => 'Votre compte ne possède pas de rôle valide. Contactez l’administratrice.',
            ]);
    }
}
