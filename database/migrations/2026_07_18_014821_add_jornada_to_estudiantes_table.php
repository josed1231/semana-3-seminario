<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Solo agrega la columna si no existe previamente
        if (!Schema::hasColumn('estudiantes', 'jornada')) {
            Schema::table('estudiantes', function (Blueprint $table) {
                $table->string('jornada')->default('Diurna');
            });
        }
    }

    public function down()
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->dropColumn('jornada');
        });
    }
};
