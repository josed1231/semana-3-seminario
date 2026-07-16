<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    use HasFactory;

    protected $table = 'docentes';
    protected $primaryKey = 'id_docente';

    protected $fillable = ['nombre_docente'];

    // Un docente puede ser tutor de muchos estudiantes
    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class, 'id_docente', 'id_docente');
    }
}