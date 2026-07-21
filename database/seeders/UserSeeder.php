<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Creamos tu usuario administrador oficial
        User::updateOrCreate(
            ['email' => 'juan.zuluaga@cotecnova.edu.co'], // Tu correo real o el institucional
            [
                'name'     => 'Juan Sebastián Zuluaga',
                'username' => 'juansezuluaga',
                'password' => Hash::make('TuClaveSeguraAqui123*'), // Usa una contraseña fuerte
                'rol'      => 'admin',
            ]
        );
    }
}