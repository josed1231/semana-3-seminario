<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'webmaster@gmail.com'], // 👈 Aquí va el correo con el que vas a iniciar sesión
            [
                'name'     => 'Web Master',
                'username' => 'webmaster',
                'password' => Hash::make('password123'),
                'rol'      => 'admin',
            ]
        );
    }
}