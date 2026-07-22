<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programas_academicos', function (Blueprint $table) {
            // 1. Eliminamos la clave foránea que apuntaba a directores_unidad
            $table->dropForeign('programas_academicos_id_docente_foreign');

            // 2. Apuntamos la relación directamente a la tabla users
            $table->foreign('id_docente')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('programas_academicos', function (Blueprint $table) {
            $table->dropForeign(['id_docente']);

            $table->foreign('id_docente')
                  ->references('id_docente')
                  ->on('directores_unidad')
                  ->nullOnDelete();
        });
    }
};
