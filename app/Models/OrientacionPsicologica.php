<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrientacionPsicologica extends Model
{
    protected $table = 'orientaciones_psicologicas';
    protected $primaryKey = 'id_orientacion';
    protected $fillable = ['codigo_estudiante', 'nivel_servicio'];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'codigo_estudiante', 'codigo_estudiante');
    }
}