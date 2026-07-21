<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // Creamos o buscamos un usuario de prueba pero estrictamente con rol 'user'
        // De esta forma deja de tener privilegios de Administrador y se eliminan las brechas
        $user = User::firstOrCreate(
            ['email' => 'test_user@example.com'],
            [
                'name' => 'Usuario de Prueba',
                'username' => 'testuser',
                'password' => Hash::make('password123'),
                'rol' => 'user' // Cambiado a rol básico de manera estricta
            ]
        );

        $categorias = Category::factory(10)->create();

        Task::factory(50)->create([
            'user_id' => $user->id,
            'category_id' => fn() => $categorias->random()->id,
        ]);
    }
}