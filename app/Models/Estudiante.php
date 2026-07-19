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

    // CORRECCIÓN: Se incluye 'id_director' para permitir el update masivo con la columna real de la base de datos
    protected $fillable = [
        'codigo_estudiante', 
        'nombre_estudiante', 
        'correo', 
        'id_programa', 
        'id_docente', // <--- Asegúrate de usar este nombre aquí
        'promedio', 
        'jornada',
    ];

    // ==========================================
    // Relaciones
    // ==========================================

    public function programa()
    {
        return $this->belongsTo(ProgramaAcademico::class, 'id_programa', 'id_programa');
    }

    // CORRECCIÓN: La llave foránea en estudiantes es 'id_docente' y la primaria en directores_unidad también es 'id_docente'
    public function directorUnidad()
    {
        // El segundo parámetro es la llave foránea en 'estudiantes', que es 'id_docente'
        // El tercer parámetro es la llave primaria en 'directores_unidad', que es 'id_docente'
        return $this->belongsTo(DirectorUnidad::class, 'id_docente', 'id_docente');
    }
    public function saberesPrevios()
    {
        return $this->hasOne(SaberesPrevios::class, 'codigo_estudiante', 'codigo_estudiante');
    }

    public function riesgo()
{
    // El segundo parámetro debe ser el nombre de la columna en la tabla riesgos_desercion
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