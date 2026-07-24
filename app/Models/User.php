<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'rol',
        'jornada',
        'otp_code',       // <--- Agregado para OTP
        'otp_expires_at', // <--- Agregado para OTP
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',       // <--- Oculta el código OTP de las respuestas JSON/JWT
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'otp_expires_at'    => 'datetime', // <--- Casteo automático de la expiración OTP
        ];
    }

    public function getCodigoEstudianteAttribute()
    {
        // Esto mostrará: EST-2026-008
        return 'EST-2026-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    // Un usuario puede tener muchas tareas
    public function tasks() 
    { 
        return $this->hasMany(Task::class);
    }

    // --- Métodos requeridos por JWT ---

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    // --- Métodos de autenticación OTP (2FA) ---

    /**
     * Genera y guarda un código OTP de 6 dígitos válido por 5 minutos.
     */
    public function generateOtp(): string
    {
        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->otp_code = $otp;
        $this->otp_expires_at = now()->addMinutes(5);
        $this->save();

        return $otp;
    }

    /**
     * Verifica si el OTP ingresado es correcto y no ha expirado.
     */
    public function verifyOtp(string $code): bool
    {
        if (!$this->otp_code || !$this->otp_expires_at) {
            return false;
        }

        if (now()->gt($this->otp_expires_at)) {
            return false;
        }

        return hash_equals((string)$this->otp_code, (string)$code);
    }
}