<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

// IMPORTANTE: Debes importar las clases para que el método call las reconozca
use Database\Seeders\DirectorUnidadSeeder;
use Database\Seeders\TaskSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llamamos a los grupos de seeders en orden
        $this->call([
            DirectorUnidadSeeder::class,
            TaskSeeder::class,
        ]);
    }
}