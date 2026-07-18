<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/RiesgoDesercion.php

class RiesgoDesercion extends Model
{
    protected $table = 'riesgos_desercion';
    
    // Cambiamos la llave primaria a la que realmente usas para identificar el registro
    protected $primaryKey = 'codigo_estudiante'; 
    
    // Le decimos a Laravel que no intente autoincrementar este valor
    public $incrementing = false;
    
    // Si tu columna no es un entero, también debes especificar el tipo
    protected $keyType = 'string';

    protected $fillable = ['codigo_estudiante', 'nivel_riesgo', 'detalles'];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'codigo_estudiante', 'codigo_estudiante');
    }
}