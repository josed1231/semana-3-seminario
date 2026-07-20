<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposToEstudiantesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            // Creamos los campos como nullable para evitar conflictos con registros existentes
            $table->string('trabaja', 10)->nullable()->after('jornada');
            $table->text('actividades_estilo_vida')->nullable()->after('trabaja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            // Revertimos los cambios en caso de un rollback
            $table->dropColumn(['trabaja', 'actividades_estilo_vida']);
        });
    }
}