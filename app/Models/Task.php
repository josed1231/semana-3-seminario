<?php
namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_limite',
        'estado',
        'user_id',
        'category_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    // Scopes
    public function scopeBuscar($query, $buscar) {
        return $query->where('titulo', 'like', "%{$buscar}%")
                     ->orWhere('descripcion', 'like', "%{$buscar}%");
    }

    public function scopeCompletadas($query) {
        return $query->where('estado', 'completado');
    }

    public function scopePendientes($query) {
        return $query->where('estado', 'pendiente');
    }
}