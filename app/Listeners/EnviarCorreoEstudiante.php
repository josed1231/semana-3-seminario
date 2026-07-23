<?php

namespace App\Listeners;

use App\Events\EstudianteActualizado;
use App\Mail\AlertaEstudianteMail;
use Illuminate\Support\Facades\Mail;

class EnviarCorreoEstudiante
{
    /**
     * Handle the event.
     */
    public function handle(EstudianteActualizado $event): void
    {
        $estudiante = $event->estudiante;

        if ($estudiante) {
            // Intenta obtener el correo desde la relación user, el modelo estudiante o el atributo directo
            $correo = $estudiante->user->email ?? $estudiante->correo ?? $estudiante->email ?? null;

            if ($correo) {
                $tipo = $event->tipo ?? 'cuestionario';
                Mail::to($correo)->send(new AlertaEstudianteMail($estudiante, $tipo));
            }
        }
    }
}