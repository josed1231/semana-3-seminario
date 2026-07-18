<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DirectorUnidad extends Model
{
    use HasFactory;

    protected $table = 'directores_unidad';
    protected $primaryKey = 'id_director';
    public $timestamps = false; // Ajusta a true si tienes created_at/updated_at
    protected $fillable = ['nombre_director', 'correo_director'];
}