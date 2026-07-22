<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrientacionPsicologica extends Model
{
    protected $table = 'orientaciones_psicologicas';
    
    // Se corrige a 'id' que es la columna real de la tabla en MariaDB
    protected $primaryKey = 'id'; 

    protected $fillable = [
        'codigo_estudiante', 
        'nivel_servicio', 
        'observaciones'
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'codigo_estudiante', 'codigo_estudiante');
    }
}