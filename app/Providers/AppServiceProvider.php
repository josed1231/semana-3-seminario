<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Cuestionario;

class CuestionarioPolicy
{
    /**
     * Determina quién puede ver la lista general.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->rol, ['admin', 'psicologo', 'dir_bienestar', 'dir_unidad']);
    }

    /**
     * Determina quién puede ver un cuestionario en específico.
     */
    public function view(User $user, Cuestionario $cuestionario): bool
    {
        return $user->id === $cuestionario->user_id || in_array($user->rol, ['admin', 'psicologo', 'dir_bienestar']);
    }

    /**
     * Determina quién puede crear/diligenciar el cuestionario.
     */
    public function create(User $user): bool
    {
        return in_array($user->rol, ['user', 'estudiante']);
    }

    /**
     * Determina quién puede actualizar sus respuestas.
     */
    public function update(User $user, Cuestionario $cuestionario): bool
    {
        return $user->id === $cuestionario->user_id;
    }

    /**
     * Determina quién puede eliminar un cuestionario.
     */
    public function delete(User $user, Cuestionario $cuestionario): bool
    {
        return $user->rol === 'admin';
    }
}