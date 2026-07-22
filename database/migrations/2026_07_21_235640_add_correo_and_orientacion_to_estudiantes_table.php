<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            if (!Schema::hasColumn('estudiantes', 'correo')) {
                $table->string('correo')->nullable();
            }
            if (!Schema::hasColumn('estudiantes', 'orientacion_automatica')) {
                $table->text('orientacion_automatica')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->dropColumn(['correo', 'orientacion_automatica']);
        });
    }
};