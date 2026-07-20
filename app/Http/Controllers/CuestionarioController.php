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
        // 1. Validar los datos (Incluyendo trabaja y actividad)
        $request->validate([
            'id_programa'               => 'required',
            'semestre'                  => 'required|integer',
            'jornada'                   => 'required|string',
            'genero'                    => 'required|string',
            'victima_confict'           => 'required|string',
            'trabaja'                   => 'nullable|string',
            'actividad'                 => 'nullable|string',
            'afectacion_academico'      => 'required|numeric',
            'afectacion_socioeconomico' => 'required|numeric',
            'afectacion_psicosocial'    => 'required|numeric',
        ]);

        $programa = DB::table('programas_academicos')->where('id_programa', $request->id_programa)->first();

        // Mapeo automático de Directores según el programa al momento de la inscripción del cuestionario
        $mapeoDirectores = [
            1 => 1, // Programa ID 1 (Ingeniería) -> Director ID 1
            2 => 3, // Programa ID 2 (Agropecuaria) -> Director ID 3
            3 => 2  // Programa ID 3 (Contaduría) -> Director ID 2
        ];
        $idDirectorCalculado = $mapeoDirectores[$request->id_programa] ?? 1;

        // 2. Guardar o actualizar estudiante asignando "trabaja" y "actividad"
        $estudiante = Estudiante::updateOrCreate(
            [
                'codigo_estudiante' => auth()->user()->codigo_estudiante
            ],
            [
                'correo'            => auth()->user()->email, 
                'nombre_estudiante' => auth()->user()->name,
                'id_programa'       => $request->id_programa,
                'id_director_unidad'=> $idDirectorCalculado, 
                'id_docente'        => $programa->id_docente ?? 1, 
                'jornada'           => $request->jornada,
                'trabaja'           => $request->trabaja,
                'actividad'         => $request->actividad, // Se asocia el campo "actividad" del formulario
                'promedio'          => 0,
            ]
        );

        // 3. Guardar saberes previos (¡CORREGIDO! Ahora sí incluye la actividad en el JSON)
        $respuestas = [
            'genero'                    => $request->genero,
            'victima_conflicto'         => $request->victima_confict,
            'actividad'                 => $request->actividad, // <-- AQUÍ SE GUARDA PARA EL RENDEREADO DEL DASHBOARD
            'afectacion_academico'      => $request->afectacion_academico,
            'afectacion_socioeconomico' => $request->afectacion_socioeconomico,
            'afectacion_psicosocial'    => $request->afectacion_psicosocial,
        ];

        DB::table('saberes_previos')->updateOrInsert(
            ['codigo_estudiante' => $estudiante->codigo_estudiante],
            [
                'semestre'   => $request->semestre,
                'respuestas' => json_encode($respuestas),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 4. Calcular nivel de riesgo automático
        $academico = (int) $request->afectacion_academico;
        $socio = (int) $request->afectacion_socioeconomico;
        $psico = (int) $request->afectacion_psicosocial;
        
        $nivelMaximo = max($academico, $socio, $psico);
        
        $nivelCalculado = 'SIN RIESGO';
        if ($nivelMaximo >= 3) {
            $nivelCalculado = 'ALTO';
        } elseif ($nivelMaximo >= 1) {
            $nivelCalculado = 'BAJO';
        }

        $estudiante->riesgo()->updateOrCreate(
            ['codigo_estudiante' => $estudiante->codigo_estudiante],
            [
                'nivel_riesgo' => $nivelCalculado,
                'detalles'     => "Puntajes - Académico: $academico, Socioeconómico: $socio, Psicosocial: $psico. Nivel máximo: $nivelMaximo",
            ]
        );

        return redirect()->route('cuestionario.success');
    }

    public function edit($codigo_estudiante)
    {
        $estudiante = Estudiante::with(['programa', 'directorUnidad', 'riesgo', 'orientacionPsicologica', 'estiloVida', 'saberesPrevios'])
            ->where('codigo_estudiante', $codigo_estudiante)
            ->firstOrFail();

        $programas = DB::table('programas_academicos')->get();
        $directores = DirectorUnidad::all(); 

        return view('estudiantes.edit', compact('estudiante', 'programas', 'directores'));
    }
}