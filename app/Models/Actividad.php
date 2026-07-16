<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;

    protected $table = 'actividades';
    protected $primaryKey = 'id_actividad';

    protected $fillable = ['nombre_actividad'];

    // Relación muchos a muchos con Estudiantes
    public function estudiantes()
    {
        return $this->belongsToMany(Estudiante::class, 'estudiante_actividad', 'id_actividad', 'codigo_estudiante');
    }
}