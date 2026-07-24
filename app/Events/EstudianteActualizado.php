<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EstudianteActualizado
{
    use Dispatchable, SerializesModels;

    public $estudiante;
    public string $tipo;

    public function __construct($estudiante, string $tipo = 'cuestionario')
    {
        $this->estudiante = $estudiante;
        $this->tipo       = $tipo;
    }
}