<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = \App\Models\Task::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   public function definition(): array {
        return [
            'titulo' => $this->faker->sentence(3),
            'descripcion' => $this->faker->paragraph(),
            'fecha_limite' => $this->faker->dateTimeBetween('now', '+1 month'),
            'estado' => $this->faker->randomElement(['pendiente', 'en_progreso', 'completado']),
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
        ];
    }

}
