<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Accepte les rôles séparés par '|' ou ','
        $rolesArray = preg_split('/[|,]/', $roles);

        if (!in_array($user->role, $rolesArray)) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}
