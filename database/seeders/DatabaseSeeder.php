<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run() {
        $user = User::factory()->create([
            'name' => 'James Cano',
            //'email' => 'jjcano4@example.com',
        ]);

        $categorias = Category::factory(10)->create();

        Task::factory(50)->create([
            'user_id' => $user->id,
            'category_id' => $categorias->random()->id,
        ]);


        
    
    }
}
