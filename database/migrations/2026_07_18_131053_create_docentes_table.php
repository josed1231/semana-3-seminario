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
        // En lugar de crearla, nos aseguramos de borrarla si quedó en la BD
        Schema::dropIfExists('docentes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Se deja vacío para evitar que se recree en un rollback
    }
};