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
        Schema::create('estudiantes', function (Blueprint $table) {
            // Usamos el código del estudiante como PK (Primary Key)
            $table->string('codigo_estudiante')->primary(); 
            
            $table->string('nombre_estudiante');
            $table->string('jornada'); // Campo añadido
            $table->string('correo');
            $table->unsignedBigInteger('id_programa');
            $table->unsignedBigInteger('id_docente');
            $table->decimal('promedio', 3, 2);
            $table->timestamps();

            // Relaciones
            $table->foreign('id_programa')
                ->references('id_programa')
                ->on('programas_academicos')
                ->onDelete('cascade');
                
            $table->foreign('id_docente')
                ->references('id_docente') // Asegúrate de que esta columna exista en directores_unidad
                ->on('directores_unidad')  // <--- APUNTA A LA TABLA ACTUAL
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
