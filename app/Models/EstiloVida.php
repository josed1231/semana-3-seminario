<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstiloVida extends Model
{
    protected $table = 'estilos_vida';
    protected $primaryKey = 'id_estilo';
    protected $fillable = ['codigo_estudiante', 'trabaja'];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'codigo_estudiante', 'codigo_estudiante');
    }
}