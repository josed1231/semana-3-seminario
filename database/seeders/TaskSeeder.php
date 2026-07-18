<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // Buscamos al usuario base que quieras usar para estas tareas
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Jose D',
                'username' => 'josed',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'rol' => 'admin'
            ]
        );

        $categorias = Category::factory(10)->create();

        Task::factory(50)->create([
            'user_id' => $user->id,
            'category_id' => fn() => $categorias->random()->id,
        ]);
    }
}