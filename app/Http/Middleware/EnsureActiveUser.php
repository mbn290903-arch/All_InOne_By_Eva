<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveUser
{
    /**
     * Empêche un compte désactivé de continuer à utiliser une session ouverte.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Vérifie que le compte connecté est toujours actif.
        if ($user && ! $user->is_active) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login.show')
                ->withErrors([
                    'email' => 'Votre compte a été désactivé. Veuillez contacter l’administratrice.',
                ]);
        }

        return $next($request);
    }
}