<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Vérifie que l'utilisateur possède l'un des rôles autorisés.
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $user = $request->user();

        // Refuse l'accès si l'utilisateur ou les rôles sont absents.
        if (! $user || empty($roles)) {
            abort(
                403,
                "Vous n'avez pas accès à cette page."
            );
        }

        // Vérifie que le rôle de l'utilisateur est autorisé.
        abort_unless(
            in_array($user->role?->name, $roles, true),
            403,
            "Vous n'avez pas accès à cette page."
        );

        return $next($request);
    }
}
