<?php

namespace App\Listeners;

use App\Events\EstudianteActualizado;
use App\Mail\AlertaEstudianteMail;
use Illuminate\Support\Facades\Mail;

class EnviarCorreoEstudiante
{
    public function handle(EstudianteActualizado $event): void
    {
        $estudiante = $event->estudiante;

        // Validamos que el estudiante tenga registrado un correo válido
        if ($estudiante && $estudiante->correo) {
            Mail::to($estudiante->correo)->send(new AlertaEstudianteMail($estudiante, 'actualizacion'));
        }
    }
}
