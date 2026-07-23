<?php

namespace App\Events;

use App\Models\Estudiante;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EstudianteActualizado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Estudiante $estudiante;
    public string $tipo;

    /**
     * Create a new event instance.
     *
     * @param Estudiante $estudiante
     * @param string $tipo Tipo de alerta: 'cuestionario', 'registro', etc.
     */
    public function __construct(Estudiante $estudiante, string $tipo = 'cuestionario')
    {
        $this->estudiante = $estudiante;
        $this->tipo = $tipo;
    }
}