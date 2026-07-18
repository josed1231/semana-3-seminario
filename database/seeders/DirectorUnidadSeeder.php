<?php

namespace Database\Seeders;

use App\Models\DirectorUnidad;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DirectorUnidadSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear los 3 Directores de Unidad
        $directores = [
            ['nombre' => 'Director Ingeniería', 'email' => 'ingenieria@gmail.com', 'user' => 'dir_ingenieria'],
            ['nombre' => 'Director Contaduría', 'email' => 'contaduria@gmail.com', 'user' => 'dir_contaduría'],
            ['nombre' => 'Director Agropecuaria', 'email' => 'agropecuaria@gmail.com', 'user' => 'dir_agropecuaria'],
        ];

        foreach ($directores as $dir) {
            DirectorUnidad::create([
                'nombre_director' => $dir['nombre'],
                'correo_director' => $dir['email']
            ]);

            User::create([
                'name'     => $dir['nombre'],
                'email'    => $dir['email'],
                'password' => Hash::make('password123'),
                'rol'      => 'dir_unidad',
                'username' => $dir['user']
            ]);
        }

        // 2. Crear el Webmaster
        User::create([
            'name'     => 'Webmaster Admin',
            'email'    => 'webmaster@gmail.com', // Ajustado a gmail.com como solicitaste
            'password' => Hash::make('password123'),
            'rol'      => 'admin',
            'username' => 'webmaster'
        ]);
    }
}