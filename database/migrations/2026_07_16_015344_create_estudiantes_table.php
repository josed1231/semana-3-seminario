<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Desactivar temporalmente la verificación de llaves foráneas
        Schema::disableForeignKeyConstraints();

        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_estudiante')->unique();
            $table->unsignedBigInteger('id_programa');
            $table->unsignedBigInteger('id_docente'); // Debe ser unsignedBigInteger
            
            // ... resto de tus columnas ...
            
            $table->foreign('id_docente')
                  ->references('id_docente')
                  ->on('directores_unidad')
                  ->onDelete('cascade');

            $table->timestamps();
        });

        // Reactivar las verificaciones
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('estudiantes');
        Schema::enableForeignKeyConstraints();
    }
};