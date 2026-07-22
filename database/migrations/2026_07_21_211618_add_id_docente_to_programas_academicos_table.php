<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programas_academicos', function (Blueprint $table) {
            if (!Schema::hasColumn('programas_academicos', 'id_docente')) {
                $table->unsignedBigInteger('id_docente')->nullable()->after('nombre_programa');
            }

            $table->foreign('id_docente')
                ->references('id_docente')
                ->on('directores_unidad')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('programas_academicos', function (Blueprint $table) {
            $table->dropForeign(['id_docente']);
            $table->dropColumn('id_docente');
        });
    }
};