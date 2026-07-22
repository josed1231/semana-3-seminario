<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DirectorUnidad;
use App\Models\Estudiante;
use App\Models\OrientacionPsicologica;
use App\Models\ProgramaAcademico;
use App\Models\User;

class EstudianteController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $rol = $user->rol ?? '';
        $searchTerm = $request->input('search');

        $query = Estudiante::with([
            'programa', 
            'directorUnidad', 
            'riesgo', 
            'orientacionPsicologica', 
            'estiloVida', 
            'saberesPrevios'
        ]);

        // Restricción dinámica por Director de Unidad
        if ($rol === 'dir_unidad') {
            $programasDelDirector = ProgramaAcademico::where('id_docente', $user->id)->pluck('id_programa');
            $query->whereIn('id_programa', $programasDelDirector);
        }

        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombre_estudiante', 'like', "%{$searchTerm}%")
                  ->orWhere('codigo_estudiante', 'like', "%{$searchTerm}%");
            });
        }

        $estudiantes = $query->get();

        // Extracción limpia de la actividad del JSON o relaciones alternativas
        foreach ($estudiantes as $estudiante) {
            if (empty($estudiante->actividades_estilo_vida)) {
                if ($estudiante->saberesPrevios && !empty($estudiante->saberesPrevios->respuestas)) {
                    $respuestasJson = is_string($estudiante->saberesPrevios->respuestas) 
                        ? json_decode($estudiante->saberesPrevios->respuestas, true) 
                        : $estudiante->saberesPrevios->respuestas;

                    $estudiante->actividad = $respuestasJson['actividad'] ?? $respuestasJson['actividades_estilo_vida'] ?? null;
                }

                if (empty($estudiante->actividad)) {
                    $estudiante->actividad = optional($estudiante->estiloVida)->actividad 
                        ?? optional($estudiante->estiloVida)->descripcion;
                }
            } else {
                $estudiante->actividad = $estudiante->actividades_estilo_vida;
            }
        }

        // Estadísticas alineadas con la Orientación Automática y relaciones psicológicas
        $totalEstudiantes = $estudiantes->count();
        $riesgoAlto       = $estudiantes->filter(fn($e) => $e->riesgo?->nivel_riesgo === 'Alto')->count();
        $riesgoMedio      = $estudiantes->filter(fn($e) => $e->riesgo?->nivel_riesgo === 'Medio')->count();
        
        $conPsico = $estudiantes->filter(fn($e) => 
            !empty($e->orientacion_automatica) || 
            ($e->orientacionPsicologica !== null && !empty($e->orientacionPsicologica->observaciones))
        )->count();

        $statsEstudiantes = [
            'total_estudiantes'    => $totalEstudiantes,
            'riesgo_alto'          => $riesgoAlto,
            'riesgo_medio'         => $riesgoMedio,
            'con_psicoorientacion' => $conPsico,
        ];

        return view('dashboard', compact('estudiantes', 'statsEstudiantes', 'searchTerm'));
    }

    public function edit($codigo_estudiante)
    {
        $estudiante = Estudiante::with([
            'programa', 
            'directorUnidad', 
            'riesgo', 
            'orientacionPsicologica', 
            'estiloVida', 
            'saberesPrevios'
        ])
        ->where('codigo_estudiante', $codigo_estudiante)
        ->firstOrFail();

        $programas  = ProgramaAcademico::all();
        $directores = User::where('rol', 'dir_unidad')->get(); 

        return view('estudiantes.edit', compact('estudiante', 'programas', 'directores'));
    }

    public function update(Request $request, $codigo_estudiante)
    {
        $user = auth()->user();
        $estudiante = Estudiante::where('codigo_estudiante', $codigo_estudiante)->firstOrFail();

        // 1. Reglas de validación completas (incluyendo encuesta y campos de riesgo/orientación)
        $rules = [
            'id_programa'            => 'required|exists:programas_academicos,id_programa',
            'nombre_estudiante'      => 'required|string|max:255',
            'correo'                 => 'nullable|email|max:255',
            'jornada'                => 'required|string',
            'semestre'               => 'nullable|integer',
            'trabaja'                => 'nullable|string',
            'actividad'              => 'nullable|string',
            'nivel_riesgo'           => 'nullable|string',
            'detalles'               => 'nullable|string',
            'orientacion_automatica' => 'nullable|string',
            'observaciones'          => 'nullable|string',
            'nivel_servicio'         => 'nullable|string',
        ];

        $request->validate($rules);

        // 2. Mapeo de Director según el programa asignado
        $programa = ProgramaAcademico::find($request->id_programa);
        $nuevoIdDirector = $estudiante->id_docente;

        if ($programa && $programa->id_docente) {
            $existeDirector = DirectorUnidad::where('id_docente', $programa->id_docente)->exists();
            if ($existeDirector) {
                $nuevoIdDirector = $programa->id_docente;
            }
        }

        // 3. Actualizar datos base del Estudiante
        $datosEstudiante = [
            'nombre_estudiante'       => $request->nombre_estudiante,
            'correo'                  => $request->correo,
            'id_programa'             => $request->id_programa,
            'id_docente'              => $nuevoIdDirector,
            'jornada'                 => $request->jornada,
            'actividades_estilo_vida' => $request->input('actividad') ?? '',
            'orientacion_automatica'  => $request->input('orientacion_automatica'),
        ];

        if ($request->has('semestre')) {
            $datosEstudiante['semestre'] = $request->semestre;
        }
        if ($request->has('trabaja')) {
            $datosEstudiante['trabaja'] = $request->trabaja;
        }

        $estudiante->update($datosEstudiante);

        // 4. Actualizar / Crear información de Riesgo (si existe relación o tabla)
        if ($request->filled('nivel_riesgo') && method_exists($estudiante, 'riesgo')) {
            $estudiante->riesgo()->updateOrCreate(
                ['codigo_estudiante' => $estudiante->codigo_estudiante],
                [
                    'nivel_riesgo' => $request->input('nivel_riesgo', 'Bajo'),
                    'detalles'     => $request->input('detalles') ?? '',
                ]
            );
        }

        // 5. Persistir Orientación Psicológica DIRECTAMENTE desde el Modelo
        // Se pasa cadena vacía ('') en lugar de NULL para evitar la falla SQL 1048
        OrientacionPsicologica::updateOrCreate(
            ['codigo_estudiante' => $estudiante->codigo_estudiante],
            [
                'nivel_servicio' => $request->input('nivel_servicio') ?? 'Tutoría Académica Standard',
                'observaciones'  => $request->input('observaciones') ?? '',
            ]
        );

        return redirect()->route('alertas.monitoreo')->with('success', 'Estudiante actualizado correctamente.');
    }

    public function destroy($codigo_estudiante)
    {
        try {
            $estudiante = Estudiante::where('codigo_estudiante', $codigo_estudiante)->firstOrFail();
            $estudiante->delete();

            return redirect()->route('alertas.monitoreo')->with('success', 'Estudiante eliminado.');
        } catch (\Exception $e) {
            return redirect()->route('alertas.monitoreo')->with('error', 'Error al eliminar el estudiante.');
        }
    }
}