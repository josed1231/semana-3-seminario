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
        // 1. Validar los datos
        $request->validate([
            'id_programa'               => 'required',
            'semestre'                  => 'required|integer',
            'jornada'                   => 'required|string',
            'genero'                    => 'required|string',
            'victima_confict'           => 'required|string',
            'afectacion_academico'      => 'required|numeric',
            'afectacion_socioeconomico' => 'required|numeric',
            'afectacion_psicosocial'    => 'required|numeric',
        ]);

        $programa = DB::table('programas_academicos')->where('id_programa', $request->id_programa)->first();

        // 2. Guardar estudiante
        $estudiante = Estudiante::updateOrCreate(
            ['correo' => auth()->user()->email],
            [
                'codigo_estudiante'  => auth()->user()->codigo_estudiante, 
                'nombre_estudiante'  => auth()->user()->name,
                'id_programa'        => $request->id_programa,
                'id_docente'         => $programa->id_docente ?? 1, 
                'jornada'            => $request->jornada,
                'promedio'           => 0,
            ]
        );

        // 3. Guardar saberes previos
        $respuestas = [
            'genero'                    => $request->genero,
            'victima_conflicto'         => $request->victima_confict,
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