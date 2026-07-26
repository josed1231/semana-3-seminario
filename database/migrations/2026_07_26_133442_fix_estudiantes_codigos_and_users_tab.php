<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Desactiva temporalmente las FK de forma segura en MySQL, PostgreSQL o SQLite
        Schema::disableForeignKeyConstraints();

        DB::transaction(function () {
            $estudiantes = DB::table('estudiantes')->get();

            foreach ($estudiantes as $index => $estudiante) {
                $codigoAntiguo = $estudiante->codigo_estudiante;

                // 1. Vincular id_user si está nulo
                $idUser = $estudiante->id_user;
                if (!$idUser) {
                    $user = User::where('email', $estudiante->correo)
                        ->orWhere('username', $estudiante->cedula)
                        ->orWhere('username', $codigoAntiguo)
                        ->first();

                    if ($user) {
                        $idUser = $user->id;
                    }
                }

                // 2. Resguardar cédula
                $cedula = $estudiante->cedula;
                if (empty($cedula) && !str_starts_with($codigoAntiguo, 'EST-')) {
                    $cedula = $codigoAntiguo;
                }

                // 3. Formatear código EST-YYYY-XXX
                $nuevoCodigo = $codigoAntiguo;
                if (!str_starts_with($codigoAntiguo, 'EST-')) {
                    $anio = date('Y');
                    $consecutivo = str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                    $nuevoCodigo = "EST-{$anio}-{$consecutivo}";
                }

                // Actualizar registro en la tabla principal
                DB::table('estudiantes')
                    ->where('codigo_estudiante', $codigoAntiguo)
                    ->update([
                        'codigo_estudiante' => $nuevoCodigo,
                        'id_user'           => $idUser,
                        'cedula'            => $cedula,
                    ]);

                // Sincronizar tablas hijas si cambió el código
                if ($codigoAntiguo !== $nuevoCodigo) {
                    DB::table('riesgo_desercion')->where('codigo_estudiante', $codigoAntiguo)->update(['codigo_estudiante' => $nuevoCodigo]);
                    DB::table('orientacion_psicologica')->where('codigo_estudiante', $codigoAntiguo)->update(['codigo_estudiante' => $nuevoCodigo]);
                    DB::table('saberes_previos')->where('codigo_estudiante', $codigoAntiguo)->update(['codigo_estudiante' => $nuevoCodigo]);
                    DB::table('estilo_vida')->where('codigo_estudiante', $codigoAntiguo)->update(['codigo_estudiante' => $nuevoCodigo]);
                }
            }
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No aplica reverso al ser migración de limpieza de datos
    }
};