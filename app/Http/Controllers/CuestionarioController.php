<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DirectorUnidad;
use App\Models\Estudiante;

class CuestionarioController extends Controller
{
    public function index()
    {
        $programas = DB::table('programas_academicos')->get();
        return view('estudiantes.create', compact('programas'));
    }

    public function create()
    {
        $programas = DB::table('programas_academicos')->get();
        $directores = DirectorUnidad::all();
        
        return view('cuestionario', compact('programas', 'directores'));
    }

    public function store(Request $request)
    {
        // 1. Validar los datos del formulario de caracterización
        $request->validate([
            'id_programa'               => 'required',
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

        $programa = DB::table('programas_academicos')->where('id_programa', $request->id_programa)->first();

        // Mapeo automático de Directores según el programa académico
        $mapeoDirectores = [
            1 => 1, // Programa ID 1 (Ingeniería) -> Director ID 1
            2 => 3, // Programa ID 2 (Agropecuaria) -> Director ID 3
            3 => 2  // Programa ID 3 (Contaduría) -> Director ID 2
        ];
        $idDirectorCalculado = $mapeoDirectores[$request->id_programa] ?? 1;

        // Limpieza y respaldo del campo actividad
        $actividadTexto = $request->input('actividad', '');

        // 2. Guardar o actualizar datos base del Estudiante
        $estudiante = Estudiante::updateOrCreate(
            [
                'codigo_estudiante' => auth()->user()->codigo_estudiante
            ],
            [
                'correo'                  => auth()->user()->email, 
                'nombre_estudiante'       => auth()->user()->name,
                'id_programa'             => $request->id_programa,
                'id_director_unidad'      => $idDirectorCalculado, 
                'id_docente'              => $programa->id_docente ?? 1, 
                'jornada'                 => $request->jornada,
                'trabaja'                 => $request->input('trabaja', 'No'),
                'actividades_estilo_vida' => $actividadTexto, // Se actualiza el campo de estilo de vida
                'actividad'               => $actividadTexto, // Se actualiza el alias directo
                'promedio'                => 0,
            ]
        );

        // 3. Guardar respuestas en saberes_previos guardando la estructura JSON completa
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
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 4. Mapeo y cálculo del nivel de riesgo compatible con ENUM / VARCHAR estricto ('Bajo', 'Medio', 'Alto')
        $academicoRaw = $request->input('afectacion_academico');
        $socioRaw     = $request->input('afectacion_socioeconomico');
        $psicoRaw     = $request->input('afectacion_psicosocial');

        // Conversión de texto a valores numéricos de ponderación
        $convertirPuntaje = function($valor) {
            if (is_numeric($valor)) return (int) $valor;
            return match (trim(mb_strtolower((string)$valor))) {
                'alta', 'alto', 'mucha afectacion', 'afectacion alta' => 3,
                'afectacion media', 'medio', 'moderado'               => 2,
                'sin afectacion', 'bajo', 'ninguna', 'no representa'  => 1,
                default                                               => 1,
            };
        };

        $academico = $convertirPuntaje($academicoRaw);
        $socio     = $convertirPuntaje($socioRaw);
        $psico     = $convertirPuntaje($psicoRaw);
        
        $nivelMaximo = max($academico, $socio, $psico);
        
        // Mapeo estricto a las opciones permitidas en la base de datos (evita error de Data Truncated)
        $nivelCalculado = 'Bajo';
        if ($nivelMaximo >= 3) {
            $nivelCalculado = 'Alto';
        } elseif ($nivelMaximo == 2) {
            $nivelCalculado = 'Medio';
        }

        // Guardado en la relación de riesgo
        $estudiante->riesgo()->updateOrCreate(
            ['codigo_estudiante' => $estudiante->codigo_estudiante],
            [
                'nivel_riesgo' => $nivelCalculado,
                'detalles'     => "Puntajes - Académico: $academicoRaw, Socioeconómico: $socioRaw, Psicosocial: $psicoRaw.",
            ]
        );

        return redirect()->route('cuestionario.success');
    }

    public function edit($codigo_estudiante)
    {
        $estudiante = Estudiante::with(['programa', 'directorUnidad', 'riesgo', 'orientacionPsicologica', 'estiloVida', 'saberesPrevios'])
            ->where('codigo_estudiante', $codigo_estudiante)
            ->firstOrFail();

        // Extracción dinámica de actividad si viene dentro de saberes_previos
        if (empty($estudiante->actividad) && empty($estudiante->actividades_estilo_vida)) {
            if ($estudiante->saberesPrevios && !empty($estudiante->saberesPrevios->respuestas)) {
                $respuestasJson = is_string($estudiante->saberesPrevios->respuestas) 
                    ? json_decode($estudiante->saberesPrevios->respuestas, true) 
                    : $estudiante->saberesPrevios->respuestas;

                $estudiante->actividad = $respuestasJson['actividad'] ?? $respuestasJson['actividades_estilo_vida'] ?? null;
            }
        }

        $programas = DB::table('programas_academicos')->get();
        $directores = DirectorUnidad::all(); 

        return view('estudiantes.edit', compact('estudiante', 'programas', 'directores'));
    }
}