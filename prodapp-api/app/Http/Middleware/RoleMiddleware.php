<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Vérifie si l'utilisateur est authentifié
        if (!Auth::check()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Vérifie si l'utilisateur a l'un des rôles requis
        $user = Auth::user();
        if (!$user->hasAnyRole($roles)) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        return $next($request);
    }
}
