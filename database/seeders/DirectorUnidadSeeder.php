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
        // 1. Crear o actualizar los 3 Directores de Unidad
        $directores = [
            ['nombre' => 'Director Ingeniería', 'email' => 'ingenieria@gmail.com', 'user' => 'dir_ingenieria'],
            ['nombre' => 'Director Contaduría', 'email' => 'contaduria@gmail.com', 'user' => 'dir_contaduría'],
            ['nombre' => 'Director Agropecuaria', 'email' => 'agropecuaria@gmail.com', 'user' => 'dir_agropecuaria'],
        ];

        foreach ($directores as $dir) {
            // Busca por correo_director; si existe lo actualiza, si no, lo crea.
            DirectorUnidad::updateOrCreate(
                ['correo_director' => $dir['email']],
                ['nombre_director' => $dir['nombre']]
            );

            // Busca por email; si existe lo actualiza, si no, lo crea.
            User::updateOrCreate(
                ['email' => $dir['email']],
                [
                    'name'     => $dir['nombre'],
                    'password' => Hash::make('password123'),
                    'rol'      => 'dir_unidad',
                    'username' => $dir['user']
                ]
            );
        }

        // 2. Crear o actualizar el Webmaster
        User::updateOrCreate(
            ['email' => 'webmaster@gmail.com'],
            [
                'name'     => 'Webmaster Admin',
                'password' => Hash::make('password123'),
                'rol'      => 'admin',
                'username' => 'webmaster'
            ]
        );
    }
}