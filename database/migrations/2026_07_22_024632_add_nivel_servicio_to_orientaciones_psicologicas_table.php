<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orientaciones_psicologicas', function (Blueprint $table) {
            // Asegurar que nivel_servicio exista
            if (!Schema::hasColumn('orientaciones_psicologicas', 'nivel_servicio')) {
                $table->string('nivel_servicio')->nullable()->after('codigo_estudiante');
            }

            // Permitir que fecha_sesion sea nula para que no exija un valor por defecto
            if (Schema::hasColumn('orientaciones_psicologicas', 'fecha_sesion')) {
                $table->dateTime('fecha_sesion')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orientaciones_psicologicas', function (Blueprint $table) {
            $table->dropColumn('nivel_servicio');
            $table->dateTime('fecha_sesion')->nullable(false)->change();
        });
    }
};