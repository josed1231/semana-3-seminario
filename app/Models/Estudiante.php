<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'estudiantes';
    protected $primaryKey = 'codigo_estudiante';
    
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codigo_estudiante',
        'nombre_estudiante',
        'jornada',
        'correo',
        'id_programa',
        'id_director_unidad',
        'valor_matricula',
        'estado_pago',
        'promedio'
    ];

    // Relaciones
    public function programa()
    {
        return $this->belongsTo(ProgramaAcademico::class, 'id_programa', 'id_programa');
    }

    public function directorUnidad()
    {
        return $this->belongsTo(DirectorUnidad::class, 'id_director_unidad', 'id_director');
    }

    public function riesgo()
    {
        return $this->hasOne(RiesgoDesercion::class, 'codigo_estudiante', 'codigo_estudiante');
    }

    public function estiloVida()
    {
        return $this->hasOne(EstiloVida::class, 'codigo_estudiante', 'codigo_estudiante');
    }

    public function orientacionPsicologica()
    {
        return $this->hasOne(OrientacionPsicologica::class, 'codigo_estudiante', 'codigo_estudiante');
    }

    public function actividades()
    {
        return $this->belongsToMany(Actividad::class, 'estudiante_actividad', 'codigo_estudiante', 'id_actividad');
    }
}