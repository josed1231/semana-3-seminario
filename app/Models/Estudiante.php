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
        'correo', 
        'id_programa', 
        'id_docente',
        'promedio', 
        'jornada',
        'trabaja',
        'actividades_estilo_vida',
        'orientacion_automatica',
    ];

    // ==========================================
    // Scopes de Búsqueda y Filtrado
    // ==========================================

    public function scopeBuscar($query, $texto)
    {
        if (empty($texto)) return $query;

        return $query->where(function($q) use ($texto) {
            $q->where('nombre_estudiante', 'LIKE', "%{$texto}%")
              ->orWhere('codigo_estudiante', 'LIKE', "%{$texto}%")
              ->orWhereHas('user', function($userQuery) use ($texto) {
                  $userQuery->where('username', 'LIKE', "%{$texto}%");
              });
        });
    }

    public function scopeFiltrarPrograma($query, $programaId)
    {
        if (empty($programaId)) return $query;
        return $query->where('id_programa', $programaId);
    }

    public function scopeFiltrarSemestre($query, $semestre)
    {
        if (empty($semestre)) return $query;
        return $query->whereHas('saberesPrevios', function($q) use ($semestre) {
            $q->where('semestre', $semestre);
        });
    }

    public function scopeFiltrarJornada($query, $jornada)
    {
        if (empty($jornada)) return $query;
        return $query->where('jornada', $jornada);
    }

    // ==========================================
    // Relaciones
    // ==========================================

    public function user()
    {
        return $this->belongsTo(User::class, 'codigo_estudiante', 'username');
    }

    public function programa()
    {
        return $this->belongsTo(ProgramaAcademico::class, 'id_programa', 'id_programa');
    }

    public function directorUnidad()
    {
        return $this->belongsTo(DirectorUnidad::class, 'id_docente', 'id_docente');
    }

    public function saberesPrevios()
    {
        return $this->hasOne(SaberesPrevios::class, 'codigo_estudiante', 'codigo_estudiante');
    }

    // Si tu modelo se llama RiesgoEstudiante, cambia RiesgoDesercion::class por RiesgoEstudiante::class
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
        return $this->belongsToMany(
            Actividad::class, 
            'estudiante_actividad',
            'codigo_estudiante',
            'id_actividad'
        );
    }
}