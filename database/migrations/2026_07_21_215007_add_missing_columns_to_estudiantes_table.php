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
        Schema::table('estudiantes', function (Blueprint $table) {
            // Se agregan las columnas faltantes verificando si no existen previamente
            if (!Schema::hasColumn('estudiantes', 'nombre_estudiante')) {
                $table->string('nombre_estudiante')->nullable()->after('codigo_estudiante');
            }
            if (!Schema::hasColumn('estudiantes', 'promedio')) {
                $table->decimal('promedio', 3, 2)->default(0.00)->after('id_docente');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            if (Schema::hasColumn('estudiantes', 'nombre_estudiante')) {
                $table->dropColumn('nombre_estudiante');
            }
            if (Schema::hasColumn('estudiantes', 'promedio')) {
                $table->dropColumn('promedio');
            }
        });
    }
};