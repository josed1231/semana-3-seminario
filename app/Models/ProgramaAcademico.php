<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramaAcademico extends Model
{
    use HasFactory;

    protected $table = 'programas_academicos';
    protected $primaryKey = 'id_programa';
    public $timestamps = false;

    protected $fillable = [
        'nombre_programa',
        'id_docente',
    ];

    /**
     * Relación directa con el usuario con rol de director de unidad
     */
    public function directorUnidad()
    {
        return $this->belongsTo(User::class, 'id_docente', 'id');
    }

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class, 'id_programa', 'id_programa');
    }
}