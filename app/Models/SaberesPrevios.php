<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaberesPrevios extends Model
{
    use HasFactory;

    // Indicamos la tabla exacta de la base de datos
    protected $table = 'saberes_previos';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'codigo_estudiante',
        'semestre',
    ];

    // Relación inversa con Estudiante (Opcional pero recomendada)
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'codigo_estudiante', 'codigo_estudiante');
    }
}