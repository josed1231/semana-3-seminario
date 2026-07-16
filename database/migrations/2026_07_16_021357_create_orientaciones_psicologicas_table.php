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
        Schema::create('orientaciones_psicologicas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_estudiante');
            $table->text('observaciones');
            $table->date('fecha_sesion');
            $table->timestamps();

            $table->foreign('codigo_estudiante')->references('codigo_estudiante')->on('estudiantes')->onDelete('cascade');
        });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orientaciones_psicologicas');
    }
};
