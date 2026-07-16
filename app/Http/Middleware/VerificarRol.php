<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    public function handle(Request $request, Closure $next, string $rol): Response
    {
        // Usamos auth()->user() para obtener al usuario que realmente inició sesión
        $user = auth()->user();

        // 1. Si no hay usuario, abortar
        if (!$user) {
            abort(403, 'Debes iniciar sesión.');
        }

        // 2. Si el rol es 'admin', permitir el acceso total a todo
        if ($user->rol === 'admin') {
            return $next($request);
        }

        // 3. Si el rol del usuario no coincide con el requerido, abortar
        if ($user->rol !== $rol) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}