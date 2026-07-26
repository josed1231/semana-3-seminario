<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'estudiantes';
    protected $primaryKey = 'codigo_estudiante';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codigo_estudiante',
        'id_user',
        'id_programa',
        'semestre',
        'jornada',
        'genero',
        'saberes_previos',
        'id_docente',
        'actividades_estilo_vida',
        'orientacion_automatica',
        'trabaja',
        'cedula',
        'correo',
        'nombre_estudiante',
    ];

    protected $casts = [
        'saberes_previos' => 'array',
    ];

    /**
     * Boot del modelo para autogenerar el código EST-YYYY-XXX si no viene definido.
     */
    protected static function booted()
    {
        static::creating(function ($estudiante) {
            if (empty($estudiante->codigo_estudiante) || !str_starts_with($estudiante->codigo_estudiante, 'EST-')) {
                $anio = date('Y');
                $ultimoNumero = static::count() + 1;
                $consecutivo = str_pad($ultimoNumero, 3, '0', STR_PAD_LEFT);

                $estudiante->codigo_estudiante = "EST-{$anio}-{$consecutivo}";
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function programa(): BelongsTo
    {
        return $this->belongsTo(ProgramaAcademico::class, 'id_programa');
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_docente');
    }

    public function riesgo(): HasOne
    {
        return $this->hasOne(RiesgoDesercion::class, 'codigo_estudiante', 'codigo_estudiante');
    }

    public function orientacionPsicologica(): HasOne
    {
        return $this->hasOne(OrientacionPsicologica::class, 'codigo_estudiante', 'codigo_estudiante');
    }

    public function saberesPrevios(): HasOne
    {
        return $this->hasOne(SaberesPrevios::class, 'codigo_estudiante', 'codigo_estudiante');
    }

    public function estiloVida(): HasOne
    {
        return $this->hasOne(EstiloVida::class, 'codigo_estudiante', 'codigo_estudiante');
    }

    /*
    |--------------------------------------------------------------------------
    | Accesores
    |--------------------------------------------------------------------------
    */

    public function getCedulaAttribute(): ?string
    {
        // 1. Prioriza siempre la cédula real alojada en el username de la relación User
        if ($this->relationLoaded('user') && $this->user && !empty($this->user->username)) {
            return $this->user->username;
        }

        if ($this->user?->username) {
            return $this->user->username;
        }

        // 2. Si no tiene usuario asociado, usa la columna física 'cedula' como fallback
        return $this->attributes['cedula'] ?? 'N/A';
    }

    public function getCorreoAttribute(): ?string
    {
        if (!empty($this->attributes['correo'])) {
            return $this->attributes['correo'];
        }
        return $this->user?->email ?? 'Sin correo';
    }

    public function getNombreEstudianteAttribute(): ?string
    {
        if (!empty($this->attributes['nombre_estudiante'])) {
            return $this->attributes['nombre_estudiante'];
        }
        return $this->user?->name ?? 'Sin nombre';
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes de Búsqueda y Filtrado
    |--------------------------------------------------------------------------
    */

    public function scopeBuscar(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->whereHas('user', function (Builder $userQuery) use ($term) {
                $userQuery->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('username', 'like', "%{$term}%");
            })
            ->orWhere('codigo_estudiante', 'like', "%{$term}%")
            ->orWhere('cedula', 'like', "%{$term}%")
            ->orWhere('nombre_estudiante', 'like', "%{$term}%");
        });
    }

    public function scopeFiltrar(Builder $query, array $filters): Builder
    {
        if (!empty($filters['programa'])) {
            $query->where('id_programa', $filters['programa']);
        }

        if (!empty($filters['semestre'])) {
            $query->where('semestre', $filters['semestre']);
        }

        if (!empty($filters['jornada'])) {
            $query->where('jornada', $filters['jornada']);
        }

        if (!empty($filters['genero'])) {
            $query->where('genero', $filters['genero']);
        }

        return $query;
    }
}