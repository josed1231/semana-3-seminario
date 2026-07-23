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

        if ($estudiante && $estudiante->correo) {
            // Se envía tipo 'registro' si es nuevo o 'actualizacion' si es una edición
            $tipo = $event->tipo ?? 'registro'; 
            Mail::to($estudiante->correo)->send(new AlertaEstudianteMail($estudiante, $tipo));
        }
    }
}