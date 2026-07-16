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
        Schema::create('estilos_vida', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_estudiante');
            $table->integer('horas_estudio_semanal');
            $table->string('trabaja');
            $table->timestamps();

            $table->foreign('codigo_estudiante')->references('codigo_estudiante')->on('estudiantes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estilos_vida');
    }
};
