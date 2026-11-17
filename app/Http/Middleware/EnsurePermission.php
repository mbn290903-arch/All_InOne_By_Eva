<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$permissions
    ): Response {
        $user = $request->user();

        // Vérifier que l'utilisateur possède au moins une permission demandée.
        $autorise = $user && collect($permissions)->isNotEmpty()
            && collect($permissions)->contains(
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
