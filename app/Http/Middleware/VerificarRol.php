<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    /**
     * Handle an incoming request.
     */
   public function handle(Request $request, Closure $next, string $rol): Response
    {
        $user = \App\Models\User::where('username', 'prueba_juan')->first();

        // Si es admin, lo dejamos pasar directo al formulario limpio
        if ($user && $user->rol === 'admin') {
            return $next($request);
        }

        if (!$user || $user->rol !== $rol) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}