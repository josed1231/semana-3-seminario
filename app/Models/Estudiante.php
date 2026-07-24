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
        'cedula', 
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

    /**
     * Atributos personalizados adjuntos al serializar el modelo (JSON/Array).
     */
    protected $appends = ['cedula'];

    /**
     * Accessor para la propiedad 'cedula'.
     * Obtiene la cédula del usuario vinculado por correo o la columna interna.
     */
    public function getCedulaAttribute()
    {
        // 1. Prioriza la cédula (username) desde la relación con el usuario
        if ($this->user && !empty($this->user->username)) {
            return $this->user->username;
        }

        // 2. Si no hay usuario vinculado, usa el campo 'cedula' propio (siempre que no sea el código)
        if (!empty($this->attributes['cedula']) && $this->attributes['cedula'] !== $this->attributes['codigo_estudiante']) {
            return $this->attributes['cedula'];
        }

        return 'N/A';
    }

    // ==========================================
    // Scopes de Búsqueda y Filtrado
    // ==========================================

    public function scopeBuscar($query, $texto)
    {
        if (empty($texto)) return $query;

        $termino = mb_strtolower(trim($texto));

        return $query->where(function($q) use ($termino) {
            $q->whereRaw('LOWER(nombre_estudiante) LIKE ?', ["%{$termino}%"])
              ->orWhereRaw('LOWER(codigo_estudiante) LIKE ?', ["%{$termino}%"])
              ->orWhereHas('user', function($userQuery) use ($termino) {
                  $userQuery->whereRaw('LOWER(username) LIKE ?', ["%{$termino}%"]);
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

    /**
     * Relación con el Modelo User enlazada mediante el correo electrónico.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'correo', 'email');
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