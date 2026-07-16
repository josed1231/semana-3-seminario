<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiesgoDesercion extends Model
{
    protected $table = 'riesgos_desercion';
    protected $primaryKey = 'id_riesgo';
    protected $fillable = ['codigo_estudiante', 'nivel_riesgo', 'aplica_beca'];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'codigo_estudiante', 'codigo_estudiante');
    }
}