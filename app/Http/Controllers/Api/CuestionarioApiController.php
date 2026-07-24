<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Estudiante;
use App\Models\ProgramaAcademico;
use App\Services\Orientacion;
use App\Events\EstudianteActualizado;

class CuestionarioApiController extends Controller
{
    /**
     * Procesa y almacena las respuestas del cuestionario desde la API REST.
     * Calcula automáticamente el riesgo de deserción y genera la ruta PIAE.
     */
    public function store(Request $request)
    {
        // 1. Validar la entrada de datos JSON
        $validated = $request->validate([
            'id_programa'               => 'required|exists:programas_academicos,id_programa',
            'semestre'                  => 'required|integer|min:1|max:12',
            'jornada'                   => 'required|string',
            'genero'                    => 'nullable|string',
            'victima_confict'           => 'nullable|string',
            'trabaja'                   => 'nullable|string',
            'actividad'                 => 'nullable|string',
            'afectacion_academico'      => 'nullable',
            'afectacion_socioeconomico' => 'nullable',
            'afectacion_psicosocial'    => 'nullable',
        ]);

        $estudianteProcesado = null;

        try {
            DB::transaction(function () use ($request, &$estudianteProcesado) {
                $user = auth()->user();

                // Determinar el código del estudiante (de la sesión JWT o del parámetro de entrada)
                $codigoEstudiante = $user->codigo_estudiante ?? $request->input('codigo_estudiante');

                if (!$codigoEstudiante) {
                    throw new \Exception('El usuario autenticado no tiene un código de estudiante asociado.');
                }

                // 2. Obtener Programa Académico y validar Director de Unidad
                $programa = ProgramaAcademico::findOrFail($request->id_programa);
                $idDocente = $programa->id_docente;

                if ($idDocente) {
                    $existeDirector = DB::table('directores_unidad')->where('id_docente', $idDocente)->exists();
                    if (!$existeDirector) {
                        $idDocente = null;
                    }
                }

                $actividadTexto = $request->input('actividad', '');

                // 3. Crear o Actualizar el Perfil Base del Estudiante
                $estudiante = Estudiante::updateOrCreate(
                    ['codigo_estudiante' => $codigoEstudiante],
                    [
                        'nombre_estudiante'       => $user->name,
                        'correo'                  => $user->email,
                        'id_programa'             => $request->id_programa,
                        'id_docente'              => $idDocente,
                        'jornada'                 => $request->jornada,
                        'trabaja'                 => $request->input('trabaja', 'No'),
                        'actividades_estilo_vida' => $actividadTexto,
                        'promedio'                => 0,
                    ]
                );

                // 4. Guardar Respuestas en saberes_previos (JSON)
                $respuestas = [
                    'genero'                    => $request->input('genero'),
                    'victima_conflicto'         => $request->input('victima_confict'),
                    'actividad'                 => $actividadTexto,
                    'actividades_estilo_vida'   => $actividadTexto,
                    'afectacion_academico'      => $request->input('afectacion_academico'),
                    'afectacion_socioeconomico' => $request->input('afectacion_socioeconomico'),
                    'afectacion_psicosocial'    => $request->input('afectacion_psicosocial'),
                ];

                DB::table('saberes_previos')->updateOrInsert(
                    ['codigo_estudiante' => $estudiante->codigo_estudiante],
                    [
                        'semestre'   => $request->semestre,
                        'respuestas' => json_encode($respuestas, JSON_UNESCAPED_UNICODE),
                        'updated_at' => now(),
                    ]
                );

                // 5. Motor de Conversión de Puntajes y Cálculo de Riesgo
                $academicoRaw = $request->input('afectacion_academico');
                $socioRaw     = $request->input('afectacion_socioeconomico');
                $psicoRaw     = $request->input('afectacion_psicosocial');

                $convertirPuntaje = function ($valor) {
                    if (is_numeric($valor)) {
                        return (int) $valor;
                    }
                    if (is_null($valor)) {
                        return 0;
                    }
                    return match (trim(mb_strtolower((string)$valor))) {
                        'alta', 'alto', 'mucha afectacion', 'afectacion alta' => 3,
                        'afectacion media', 'medio', 'moderado'              => 2,
                        'sin afectacion', 'bajo', 'ninguna', 'no representa'  => 1,
                        default                                             => 1,
                    };
                };

                $academico = $convertirPuntaje($academicoRaw);
                $socio     = $convertirPuntaje($socioRaw);
                $psico     = $convertirPuntaje($psicoRaw);

                $nivelMaximo = max($academico, $socio, $psico);

                $nivelCalculado = 'Bajo';
                if ($nivelMaximo >= 3) {
                    $nivelCalculado = 'Alto';
                } elseif ($nivelMaximo == 2) {
                    $nivelCalculado = 'Medio';
                }

                $detallesPuntajes = "Puntajes - Académico: {$academicoRaw}, Socioeconómico: {$socioRaw}, Psicosocial: {$psicoRaw}.";

                // Guardar/Actualizar Riesgo de Deserción
                $estudiante->riesgo()->updateOrCreate(
                    ['codigo_estudiante' => $estudiante->codigo_estudiante],
                    [
                        'nivel_riesgo' => $nivelCalculado,
                        'detalles'     => $detallesPuntajes,
                    ]
                );

                // 6. Generación de Orientación Psicológica y Ruta PIAE
                Orientacion::generarYGuardar($estudiante, [
                    'afectacion_academico'      => $academicoRaw,
                    'afectacion_socioeconomico' => $socioRaw,
                    'afectacion_psicosocial'    => $psicoRaw,
                ]);

                // Asignar estudiante a la variable de salida
                $estudianteProcesado = $estudiante;
            });

            // 7. Notificación mediante Evento (Fuera de la transacción de BD)
            if ($estudianteProcesado) {
                event(new EstudianteActualizado($estudianteProcesado, 'cuestionario'));
                $estudianteProcesado->load('orientacionPsicologica', 'riesgo');
            }

            // 8. Respuesta JSON Exitosa
            return response()->json([
                'status'  => 'success',
                'code'    => 201,
                'message' => 'Cuestionario procesado y nivel de riesgo calculated exitosamente.',
                'data'    => [
                    'codigo_estudiante'       => $estudianteProcesado->codigo_estudiante,
                    'nombre_estudiante'       => $estudianteProcesado->nombre_estudiante,
                    'nivel_riesgo_calculado'  => $estudianteProcesado->riesgo->nivel_riesgo ?? 'Bajo',
                    'detalles_riesgo'         => $estudianteProcesado->riesgo->detalles ?? '',
                    'orientacion_psicologica' => [
                        'nivel_servicio' => $estudianteProcesado->orientacionPsicologica->nivel_servicio ?? 'Tutoría Académica Standard',
                        'observaciones'  => $estudianteProcesado->orientacionPsicologica->observaciones ?? ''
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error en API Cuestionario: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => 'Ocurrió un error al procesar el cuestionario.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Devuelve las respuestas del cuestionario y estado actual del estudiante autenticado.
     */
    public function show()
    {
        $user = auth()->user();
        $codigoEstudiante = $user->codigo_estudiante;

        $estudiante = Estudiante::with(['programa', 'saberesPrevios', 'riesgo', 'orientacionPsicologica'])
            ->where('codigo_estudiante', $codigoEstudiante)
            ->first();

        if (!$estudiante) {
            return response()->json([
                'status'  => 'error',
                'message' => 'El estudiante no ha completado el cuestionario de caracterización.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $estudiante
        ], 200);
    }
}