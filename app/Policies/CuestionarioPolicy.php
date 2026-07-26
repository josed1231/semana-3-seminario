<?php

namespace App\Policies;

use App\Models\User;

class CuestionarioPolicy
{
    /**
     * Determina si el usuario puede ver la lista o resultados general del cuestionario.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->rol, [
            'admin', 
            'dir_bienestar', 
            'dir_unidad', 
            'psicologo', 
            'docente'
        ]);
    }

    /**
     * Determina si el usuario puede diligenciar / responder el cuestionario.
     */
    public function create(User $user): bool
    {
        return in_array($user->rol, ['user', 'estudiante', 'admin', 'dir_bienestar']);
    }

    /**
     * Determina si el usuario puede actualizar respuestas del cuestionario.
     */
    public function update(User $user): bool
    {
        return in_array($user->rol, ['user', 'estudiante', 'admin', 'dir_bienestar', 'psicologo']);
    }

    /**
     * Determina si el usuario puede eliminar registros asociados.
     */
    public function delete(User $user): bool
    {
        return $user->rol === 'admin';
    }
}