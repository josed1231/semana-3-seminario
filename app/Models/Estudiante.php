<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'estudiantes';
    protected $primaryKey = 'codigo_estudiante';
    
    // Al ser un código tipo texto (ej: EST-2026-001) desactivamos el autoincremento
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codigo_estudiante',
        'nombre_estudiante',
        'correo',
        'id_programa',
        'id_docente',
        'valor_matricula',
        'estado_pago',
        'promedio'
    ];

    // Relación con el Programa Académico (Carrera)
    public function programa()
    {
        return $this->belongsTo(ProgramaAcademico::class, 'id_programa', 'id_programa');
    }

    // Relación con el Docente Tutor
    public function docente()
    {
        return $this->belongsTo(Docente::class, 'id_docente', 'id_docente');
    }

    // Relación de un estudiante con su nivel de riesgo de deserción
    public function riesgo()
    {
        return $this->hasOne(RiesgoDesercion::class, 'codigo_estudiante', 'codigo_estudiante');
    }

    // Relación con su estilo de vida
    public function estiloVida()
    {
        return $this->hasOne(EstiloVida::class, 'codigo_estudiante', 'codigo_estudiante');
    }

    // Relación con su orientación psicológica (Antes servicio_psicologico)
    public function orientacionPsicologica()
    {
        return $this->hasOne(OrientacionPsicologica::class, 'codigo_estudiante', 'codigo_estudiante');
    }

    // Relación muchos a muchos con Actividades Extracurriculares
    public function actividades()
    {
        return $this->belongsToMany(Actividad::class, 'estudiante_actividad', 'codigo_estudiante', 'id_actividad');
    }
}