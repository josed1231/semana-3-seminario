<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Estudiante;
use App\Models\ProgramaAcademico;
use App\Models\User;
use App\Services\Orientacion;
use App\Events\EstudianteActualizado;

class CuestionarioController extends Controller
{
    /**
     * Muestra el formulario de registro de estudiantes (Vista Admin / Bienestar)
     */
    public function index()
    {
        $programas = ProgramaAcademico::with('directorUnidad')->get();
        
        return view('estudiantes.create', compact('programas'));
    }

    /**
     * Muestra el cuestionario de caracterización para el estudiante
     */
    public function create()
    {
        $programas = ProgramaAcademico::with('directorUnidad')->get();
        $directores = User::where('rol', 'dir_unidad')->get();
        
        return view('cuestionario', compact('programas', 'directores'));
    }

    /**
     * Almacena/actualiza las respuestas del cuestionario y calcula el nivel de riesgo
     */
    public function store(Request $request)
    {
        // 1. Validar los datos del formulario de caracterización
        $request->validate([
            'id_programa'               => 'required|exists:programas_academicos,id_programa',
            'semestre'                  => 'required|integer',
            'jornada'                   => 'required|string',
            'genero'                    => 'nullable|string',
            'victima_confict'           => 'nullable|string',
            'trabaja'                   => 'nullable|string',
            'actividad'                 => 'nullable|string',
            'afectacion_academico'      => 'nullable',
            'afectacion_socioeconomico' => 'nullable',
            'afectacion_psicosocial'    => 'nullable',
        ]);

        // Consulta del programa académico seleccionado
        $programa = ProgramaAcademico::findOrFail($request->id_programa);

        // Obtener el ID del director de unidad asociado al programa
        $idDocente = $programa->id_docente;

        // PROTECCIÓN: Verificar si el id_docente realmente existe en la tabla directores_unidad
        if ($idDocente) {
            $existeDirector = DB::table('directores_unidad')->where('id_docente', $idDocente)->exists();
            
            if (!$existeDirector) {
                $idDocente = null;
            }
        }

        // Limpieza del campo actividad
        $actividadTexto = $request->input('actividad', '');

        // 2. Guardar o actualizar datos base del Estudiante (Sincronización automática de correo)
        $estudiante = Estudiante::updateOrCreate(
            [
                'codigo_estudiante' => auth()->user()->codigo_estudiante
            ],
            [
                'nombre_estudiante'       => auth()->user()->name,
                'correo'                  => auth()->user()->email, // <-- CORRECCIÓN: Asigna el correo automáticamente
                'id_programa'             => $request->id_programa,
                'id_docente'              => $idDocente,
                'jornada'                 => $request->jornada,
                'trabaja'                 => $request->input('trabaja', 'No'),
                'actividades_estilo_vida' => $actividadTexto,
                'promedio'                => 0,
            ]
        );

        // 3. Guardar respuestas en saberes_previos (Estructura JSON)
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

        // 4. Mapeo y cálculo del nivel de riesgo
        $academicoRaw = $request->input('afectacion_academico');
        $socioRaw     = $request->input('afectacion_socioeconomico');
        $psicoRaw     = $request->input('afectacion_psicosocial');

        $convertirPuntaje = function($valor) {
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

        // Al guardar en 'riesgo()', el Observer RiesgoDesercionObserver reaccionará automáticamente
        $estudiante->riesgo()->updateOrCreate(
            ['codigo_estudiante' => $estudiante->codigo_estudiante],
            [
                'nivel_riesgo' => $nivelCalculado,
                'detalles'     => "Puntajes - Académico: $academicoRaw, Socioeconómico: $socioRaw, Psicosocial: $psicoRaw.",
            ]
        );

        // 5. Generación directa de la Orientación Psicológica
        Orientacion::generarYGuardar($estudiante, [
            'afectacion_academico'      => $academicoRaw,
            'afectacion_socioeconomico' => $socioRaw,
            'afectacion_psicosocial'    => $psicoRaw,
        ]);

        // 6. Disparar el evento para notificar al estudiante la finalización del cuestionario
        event(new EstudianteActualizado($estudiante, 'cuestionario'));

        return redirect()->route('cuestionario.success');
    }

    /**
     * Muestra el formulario para editar a un estudiante
     */
    public function edit($codigo_estudiante)
    {
        $estudiante = Estudiante::with([
            'programa.directorUnidad', 
            'riesgo', 
            'orientacionPsicologica', 
            'estiloVida', 
            'saberesPrevios'
        ])
        ->where('codigo_estudiante', $codigo_estudiante)
        ->firstOrFail();

        if (empty($estudiante->actividades_estilo_vida)) {
            if ($estudiante->saberesPrevios && !empty($estudiante->saberesPrevios->respuestas)) {
                $respuestasJson = is_string($estudiante->saberesPrevios->respuestas) 
                    ? json_decode($estudiante->saberesPrevios->respuestas, true) 
                    : $estudiante->saberesPrevios->respuestas;

                $estudiante->actividades_estilo_vida = $respuestasJson['actividad'] ?? $respuestasJson['actividades_estilo_vida'] ?? null;
            }
        }

        $programas = ProgramaAcademico::with('directorUnidad')->get();
        $directores = User::where('rol', 'dir_unidad')->get(); 

        return view('estudiantes.edit', compact('estudiante', 'programas', 'directores'));
    }

    /**
     * Actualiza la información de un estudiante desde el formulario de edición (Admin)
     */
    public function update(Request $request, $codigo_estudiante)
    {
        $estudiante = Estudiante::where('codigo_estudiante', $codigo_estudiante)->firstOrFail();

        $request->validate([
            'id_programa'               => 'required|exists:programas_academicos,id_programa',
            'jornada'                   => 'required|string',
            'trabaja'                   => 'nullable|string',
            'afectacion_academico'      => 'nullable',
            'afectacion_socioeconomico' => 'nullable',
            'afectacion_psicosocial'    => 'nullable',
        ]);

        // Actualizar estudiante
        $estudiante->update([
            'id_programa' => $request->id_programa,
            'jornada'     => $request->jornada,
            'trabaja'     => $request->input('trabaja', 'No'),
        ]);

        // Recalcular y guardar la orientación psicológica si se enviaron las afectaciones
        $academicoRaw = $request->input('afectacion_academico');
        $socioRaw     = $request->input('afectacion_socioeconomico');
        $psicoRaw     = $request->input('afectacion_psicosocial');

        if ($academicoRaw || $socioRaw || $psicoRaw) {
            Orientacion::generarYGuardar($estudiante, [
                'afectacion_academico'      => $academicoRaw,
                'afectacion_socioeconomico' => $socioRaw,
                'afectacion_psicosocial'    => $psicoRaw,
            ]);
        }

        return redirect()->route('estudiantes.index')->with('success', 'Estudiante y orientación actualizados correctamente.');
    }
}