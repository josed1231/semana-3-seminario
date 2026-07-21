<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    /**
     * Maneja las peticiones entrantes evaluando si el rol del usuario
     * se encuentra dentro de la lista de roles permitidos.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        // 1. Si no hay usuario autenticado
        if (!$user) {
            abort(403, 'Debes iniciar sesión.');
        }

        // 2. Si el rol es 'admin', acceso total garantizado
        if ($user->rol === 'admin') {
            return $next($request);
        }

        // 3. Verificar si el rol del usuario está entre los roles permitidos en la ruta
        if (!in_array($user->rol, $roles)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}