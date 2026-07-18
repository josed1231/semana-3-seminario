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
        Schema::table('estudiantes', function (Blueprint $table) {
            // Usamos 'Diurna' como valor por defecto para evitar el error 1364
            $table->string('jornada')->default('Diurna'); 
        });
    }

    public function down()
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->dropColumn('jornada');
        });
    }
};
