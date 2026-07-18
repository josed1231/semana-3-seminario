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
        Schema::table('saberes_previos', function (Blueprint $table) {
            // Solo intenta agregarla si no existe en la tabla
            if (!Schema::hasColumn('saberes_previos', 'respuestas')) {
                $table->string('respuestas')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('saberes_previos', function (Blueprint $table) {
            if (Schema::hasColumn('saberes_previos', 'respuestas')) {
                $table->dropColumn('respuestas');
            }
        });
    }
};
