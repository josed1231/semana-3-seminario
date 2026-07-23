<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\ProgramaAcademico;
use App\Models\DirectorUnidad;
use App\Models\OrientacionPsicologica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EstudianteController extends Controller
{
    /**
     * Muestra la información del estudiante para edición (Soporta Vista o Modal/JSON).
     */
    public function edit($codigo_estudiante)
    {
        try {
            $estudiante = Estudiante::with(['programa', 'directorUnidad', 'riesgo', 'orientacionPsicologica', 'saberesPrevios'])
                ->where('codigo_estudiante', $codigo_estudiante)
                ->firstOrFail();

            // Si la petición es AJAX/Modal (devuelve JSON)
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json($estudiante);
            }

            // Si es una página independiente
            $programas = ProgramaAcademico::all();
            return view('estudiantes.edit', compact('estudiante', 'programas'));

        } catch (\Exception $e) {
            Log::error('Error al obtener estudiante para edición: ' . $e->getMessage());
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['error' => 'Estudiante no encontrado'], 404);
            }
            return redirect()->back()->with('error', 'Estudiante no encontrado.');
        }
    }

    /**
     * Actualiza la información del estudiante.
     */
    public function update(Request $request, $codigo_estudiante)
    {
        $estudiante = Estudiante::where('codigo_estudiante', $codigo_estudiante)->firstOrFail();

        try {
            DB::transaction(function () use ($request, $estudiante) {
                // 1. Determinar el Director de Unidad o Docente asignado
                $programa = ProgramaAcademico::find($request->id_programa);
                $nuevoIdDirector = $estudiante->id_docente;

                if ($programa && $programa->id_docente) {
                    $existeDirector = DirectorUnidad::where('id_docente', $programa->id_docente)->exists();
                    if ($existeDirector) {
                        $nuevoIdDirector = $programa->id_docente;
                    }
                }

                // 2. Actualizar datos base del Estudiante (INCLUYENDO CÉDULA)
                $datosEstudiante = [
                    'cedula'                  => $request->input('cedula'), // <-- ¡Agregado!
                    'nombre_estudiante'       => $request->nombre_estudiante,
                    'correo'                  => $request->correo,
                    'id_programa'             => $request->id_programa,
                    'id_docente'              => $nuevoIdDirector,
                    'jornada'                 => $request->jornada,
                    'actividades_estilo_vida' => $request->input('actividad', $request->input('actividades_estilo_vida', '')),
                    'orientacion_automatica'  => $request->input('orientacion_automatica'),
                ];

                if ($request->has('semestre')) {
                    $datosEstudiante['semestre'] = $request->semestre;
                }
                if ($request->has('trabaja')) {
                    $datosEstudiante['trabaja'] = $request->trabaja;
                }

                $estudiante->update($datosEstudiante);

                // 3. Actualizar / Crear información de Riesgo
                if ($request->filled('nivel_riesgo')) {
                    $estudiante->riesgo()->updateOrCreate(
                        ['codigo_estudiante' => $estudiante->codigo_estudiante],
                        [
                            'nivel_riesgo' => $request->input('nivel_riesgo', 'Bajo'),
                            'detalles'     => $request->input('detalles', ''),
                        ]
                    );
                }

                // 4. Persistir Orientación Psicológica evitando errores NULL (SQL 1048)
                OrientacionPsicologica::updateOrCreate(
                    ['codigo_estudiante' => $estudiante->codigo_estudiante],
                    [
                        'nivel_servicio' => $request->input('nivel_servicio', 'Tutoría Académica Standard'),
                        'observaciones'  => $request->input('observaciones', ''),
                    ]
                );
            });

            return redirect()->route('alertas.monitoreo')->with('success', 'Estudiante actualizado correctamente.');

        } catch (\Exception $e) {
            Log::error('Error al actualizar estudiante: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar el estudiante.')->withInput();
        }
    }

    /**
     * Elimina un estudiante.
     */
    public function destroy($codigo_estudiante)
    {
        try {
            $estudiante = Estudiante::where('codigo_estudiante', $codigo_estudiante)->firstOrFail();
            $estudiante->delete();

            return redirect()->route('alertas.monitoreo')->with('success', 'Estudiante eliminado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar estudiante: ' . $e->getMessage());
            return redirect()->route('alertas.monitoreo')->with('error', 'Error al eliminar el estudiante.');
        }
    }
}