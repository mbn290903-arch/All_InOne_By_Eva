<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Vérifie que l'utilisateur possède le rôle administrateur.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Seul un administrateur peut accéder à ces routes.
        abort_unless(
            $user && $user->role?->name === 'admin',
            403,
            "Vous n'avez pas accès à cette page."
        );

        return $next($request);
    }
}
