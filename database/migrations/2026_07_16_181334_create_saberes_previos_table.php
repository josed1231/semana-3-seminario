<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('saberes_previos', function (Blueprint $table) {
            $table->id();
            
            // Relación con tu tabla estudiantes
            $table->string('codigo_estudiante'); 
            
            // Semestre para la lógica de solo 1er semestre
            $table->integer('semestre');
            
            // Aquí guardas las preguntas del CSV como un objeto JSON
            $table->json('respuestas'); 
            
            $table->timestamps();
            
            // Relación foránea (asegura integridad de datos)
            $table->foreign('codigo_estudiante')
                  ->references('codigo_estudiante')
                  ->on('estudiantes')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saberes_previos');
    }
};