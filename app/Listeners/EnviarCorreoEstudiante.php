<?php

namespace App\Listeners;

use App\Events\EstudianteActualizado;
use App\Mail\AlertaEstudianteMail;
use Illuminate\Contracts\Queue\ShouldQueue; // <-- AGREGADO
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EnviarCorreoEstudiante implements ShouldQueue // <-- AGREGADO ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(EstudianteActualizado $event): void
    {
        $objeto = $event->estudiante;

        if (!$objeto) {
            return;
        }

        // Intenta obtener el correo tanto si es un modelo User como Estudiante
        $correo = $objeto->email ?? $objeto->correo ?? $objeto->user->email ?? null;

        if ($correo) {
            try {
                $tipo = $event->tipo ?? 'cuestionario';
                Mail::to($correo)->send(new AlertaEstudianteMail($objeto, $tipo));
            } catch (\Exception $e) {
                Log::error("Error enviando correo de alerta ({$event->tipo}) a {$correo}: " . $e->getMessage());
            }
        }
    }
}