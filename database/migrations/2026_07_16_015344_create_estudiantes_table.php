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
        $table->id();
        $table->string('codigo_estudiante')->unique();
        $table->string('nombre_estudiante');
        $table->string('correo');
        $table->unsignedBigInteger('id_programa');
        $table->unsignedBigInteger('id_docente');
        $table->decimal('promedio', 3, 2); // Para guardar promedios como 4.50, 3.80, etc.
        $table->timestamps();

        // Si quieres añadir llaves foráneas para relacionarlos de forma segura:
        // Busca esta sección y déjala exactamente así:
    $table->foreign('id_programa')
      ->references('id_programa') // Cambiado de 'id' a 'id_programa'
      ->on('programas_academicos') // Cambiado de 'programas' a 'programas_academicos'
      ->onDelete('cascade');
    $table->foreign('id_docente')
      ->references('id_docente') // Debe decir id_docente, NO id
      ->on('docentes')
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
