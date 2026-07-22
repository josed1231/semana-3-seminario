<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirectorUnidad extends Model
{
    protected $table = 'directores_unidad';
    protected $primaryKey = 'id_docente';
    public $timestamps = false; // Ajusta a true si tu tabla usa created_at/updated_at

    protected $fillable = [
        'nombre_director',
        'correo_director',
    ];

    /**
     * Relación directa con los programas académicos que coordina este director.
     */
    public function programas()
    {
        return $this->hasMany(ProgramaAcademico::class, 'id_docente', 'id_docente');
    }

    /**
     * Relación con los estudiantes que tienen asignado este director.
     */
    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class, 'id_docente', 'id_docente');
    }
}