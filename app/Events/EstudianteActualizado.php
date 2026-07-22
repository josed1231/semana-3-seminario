<?php

namespace App\Events;

use App\Models\Estudiante;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EstudianteActualizado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Define la propiedad pública para que el listener pueda acceder a ella
    public Estudiante $estudiante;

    public function __construct(Estudiante $estudiante)
    {
        $this->estudiante = $estudiante;
    }
}
