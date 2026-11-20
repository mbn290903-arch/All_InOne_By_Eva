<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    /**
     * Vérifie que l'utilisateur possède au moins une permission demandée.
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$permissions
    ): Response {
        $user = $request->user();

        // Refuser si aucun utilisateur ou aucune permission n'est fourni.
        if (! $user || empty($permissions)) {
            abort(403, "Vous n'avez pas la permission d'effectuer cette action.");
        }

        // L'administrateur dispose de tous les accès.
        if ($user->isAdmin()) {
            return $next($request);
        }

        // L'utilisateur doit posséder au moins une permission demandée.
        $autorise = collect($permissions)->contains(
            fn (string $permission) => $user->hasPermission($permission)
        );

        abort_unless(
            $autorise,
            403,
            "Vous n'avez pas la permission d'effectuer cette action."
        );

        return $next($request);
    }
}
