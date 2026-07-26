<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Insertar valores por defecto para no dejar la tabla vacía
        DB::table('generos')->insert([
            ['nombre' => 'Masculino', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Femenino', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Otro', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('generos');
    }
};