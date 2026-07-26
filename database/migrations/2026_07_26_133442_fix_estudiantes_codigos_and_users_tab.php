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

                // 2. Resguardar la cédula si el código actual era la cédula
                $cedula = $estudiante->cedula;
                if (empty($cedula) && !str_starts_with($codigoAntiguo, 'EST-')) {
                    $cedula = $codigoAntiguo;
                }

                // Si el código ya tiene el formato EST-..., solo actualizar id_user y cédula
                if (str_starts_with($codigoAntiguo, 'EST-')) {
                    DB::table('estudiantes')
                        ->where('codigo_estudiante', $codigoAntiguo)
                        ->update([
                            'id_user' => $idUser,
                            'cedula'  => $cedula,
                        ]);
                    continue;
                }

                // 3. Generar el nuevo código formateado
                $anio = date('Y');
                $consecutivo = str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                $nuevoCodigo = "EST-{$anio}-{$consecutivo}";

                // 4. Clonar el registro y remover 'id' para evitar conflicto en estudiantes_pkey
                $nuevoRegistro = (array) $estudiante;
                unset($nuevoRegistro['id']); // Permite a Postgres asignar un ID nuevo

                $nuevoRegistro['codigo_estudiante'] = $nuevoCodigo;
                $nuevoRegistro['id_user']           = $idUser;
                $nuevoRegistro['cedula']            = $cedula;

                // Insertar el nuevo estudiante
                DB::table('estudiantes')->insert($nuevoRegistro);

                // Re-vincular las tablas hijas al nuevo código
                $tablasHijas = [
                    'riesgos_desercion',
                    'riesgo_desercion',
                    'orientacion_psicologica',
                    'saberes_previos',
                    'estilo_vida'
                ];

                foreach ($tablasHijas as $tabla) {
                    if (Schema::hasTable($tabla)) {
                        DB::table($tabla)
                            ->where('codigo_estudiante', $codigoAntiguo)
                            ->update(['codigo_estudiante' => $nuevoCodigo]);
                    }
                }

                // Eliminar el registro antiguo una vez desvinculado
                DB::table('estudiantes')
                    ->where('codigo_estudiante', $codigoAntiguo)
                    ->delete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No aplica para migraciones de normalización de datos
    }
};