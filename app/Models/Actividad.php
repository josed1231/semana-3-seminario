<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;

    // Nombre exacto de la tabla en la base de datos
    protected $table = 'actividad'; 

    protected $primaryKey = 'id_actividad';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'nombre',
        'descripcion',
        // Agrega aquí los demás campos de tu tabla si existen
    ];

    // Relación de muchos a muchos con Estudiante
    public function estudiantes()
    {
        return $this->belongsToMany(
            Estudiante::class,
            'estudiante_actividad', // Tabla intermedia
            'id_actividad',         // FK de Actividad en la intermedia
            'codigo_estudiante'     // FK de Estudiante en la intermedia
        );
    }
}