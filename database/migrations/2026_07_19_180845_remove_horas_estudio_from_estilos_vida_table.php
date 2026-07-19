<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estilos_vida', function (Blueprint $table) {
            // Eliminamos la columna que ya no es necesaria
            $table->dropColumn('horas_estudio_semanal');
        });
    }

    public function down(): void
    {
        Schema::table('estilos_vida', function (Blueprint $table) {
            // Por si necesitas revertir la migración en el futuro
            $table->integer('horas_estudio_semanal')->nullable();
        });
    }
};