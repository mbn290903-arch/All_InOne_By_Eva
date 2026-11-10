<?php

namespace App\Http\Middleware;

use App\Enums\Role as R;
use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless(
            $request->user()?->role?->name === R::Admin->value,
            403,
            "Accès réservé à l'administratrice."
        );

        return $next($request);
    }
}