<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    /**
     * Maneja las peticiones entrantes evaluando si el rol del usuario
     * se encuentra dentro de la lista de roles permitidos[cite: 18].
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        // 1. Si no hay usuario autenticado, redirigir al login o retornar JSON si es API
        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'No autenticado.'], 401);
            }
            return redirect()->route('login');
        }

        // Obtener el rol de forma segura soportando variantes (rol / role)
        $userRol = $user->rol ?? $user->role ?? '';

        // 2. Si el rol es 'admin', acceso total garantizado[cite: 18]
        if ($userRol === 'admin') {
            return $next($request);
        }

        // Normalizar los roles permitidos (admite múltiples parámetros o cadenas separadas por coma)
        $rolesPermitidos = [];
        foreach ($roles as $rol) {
            $rolesPermitidos = array_merge($rolesPermitidos, explode(',', $rol));
        }

        // 3. Si el rol no está permitido, redirigir a 'welcome' o retornar JSON si es API[cite: 18]
        if (!in_array($userRol, $rolesPermitidos, true)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Acceso no autorizado para este rol.'], 403);
            }
            return redirect()->route('welcome')->with('error', 'No tienes los permisos necesarios para acceder a este módulo.');
        }

        return $next($request);
    }
}