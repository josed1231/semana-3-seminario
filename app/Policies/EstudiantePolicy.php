<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Estudiante;

class EstudiantePolicy
{
    /**
     * ¿Quiénes pueden ver la lista general de alertas / monitoreo?
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
     * ¿Quiénes pueden ver la ficha / detalle de un estudiante?
     */
    public function view(User $user, Estudiante $estudiante): bool
    {
        // El estudiante puede ver su propia información, o el personal administrativo y académico
        return $user->email === $estudiante->correo 
            || in_array($user->rol, ['admin', 'dir_bienestar', 'dir_unidad', 'psicologo', 'docente']);
    }

    /**
     * ¿Quiénes pueden registrar / diligenciar la caracterización?
     */
    public function create(User $user): bool
    {
        return in_array($user->rol, ['user', 'estudiante', 'admin', 'dir_bienestar']);
    }

    /**
     * ¿Quiénes pueden editar datos, riesgos u orientación del estudiante?
     */
    public function update(User $user, Estudiante $estudiante): bool
    {
        // El estudiante (su propio perfil), Admin, Bienestar, Director de Unidad y Psicólogo
        return $user->email === $estudiante->correo 
            || in_array($user->rol, ['admin', 'dir_bienestar', 'dir_unidad', 'psicologo']);
    }

    /**
     * ¿Quiénes pueden eliminar un registro de estudiante?
     */
    public function delete(User $user, Estudiante $estudiante): bool
    {
        // Exclusivo para el Administrador
        return $user->rol === 'admin';
    }
}