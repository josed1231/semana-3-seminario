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
        // Cambiamos 'programas' por 'programas_academicos'
        Schema::create('programas_academicos', function (Blueprint $table) {
            $table->id('id_programa'); // Definimos la llave primaria exactamente como 'id_programa'
            $table->string('nombre_programa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programas');
    }
};
