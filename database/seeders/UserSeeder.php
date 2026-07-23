<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Se busca por correo y se crea o actualiza el registro
        User::updateOrCreate(
            ['email' => 'webmaster@gmail.com'], // O 'webmaster@cotecnova.edu.co'
            [
                'name'     => 'Web Master',
                'username' => 'webmaster', // Alias limpio sin dominio
                'password' => Hash::make('password123'), // Recuerda cambiarla después
                'rol'      => 'admin',
            ]
        );
    }
}