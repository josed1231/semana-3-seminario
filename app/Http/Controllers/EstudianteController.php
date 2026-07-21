<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DirectorUnidad;
use App\Models\Estudiante;

class EstudianteController extends Controller
{
    // Listado de estudiantes y estadísticas del Dashboard / Monitoreo
    public function index(Request $request)
    {
        $user = auth()->user();
        $rol = $user->rol;
        $searchTerm = $request->input('search');

        // Carga de relaciones necesarias para el dashboard / monitoreo
        $query = Estudiante::with([
            'programa', 
            'directorUnidad', 
            'riesgo', 
            'orientacionPsicologica', 
            'estiloVida', 
            'saberesPrevios'
        ]);

        // Filtro por unidad académica únicamente para directores de unidad
        if ($rol === 'dir_unidad') {
            $map = [
                'dir_ingenieria'   => 1, 
                'dir_agropecuaria' => 2, 
                'dir_contaduria'   => 3
            ];
            $query->where('id_programa', $map[$user->username] ?? 0);
        }

        // Buscador por nombre o código
        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombre_estudiante', 'like', "%{$searchTerm}%")
                  ->orWhere('codigo_estudiante', 'like', "%{$searchTerm}%");
            });
        }

        $estudiantes = $query->get();

        // SOPORTE Y EXTRACCIÓN DINÁMICA DE ACTIVIDADES / ESTILO DE VIDA
        foreach ($estudiantes as $estudiante) {
            if (empty($estudiante->actividad) && empty($estudiante->actividades_estilo_vida)) {
                
                // Opción A: Intentar decodificar el campo 'respuestas' en formato JSON de saberes_previos
                if ($estudiante->saberesPrevios && !empty($estudiante->saberesPrevios->respuestas)) {
                    $respuestasJson = json_decode($estudiante->saberesPrevios->respuestas, true);
                    if (isset($respuestasJson['actividad'])) {
                        $estudiante->actividad = $respuestasJson['actividad'];
                    } elseif (isset($respuestasJson['actividades_estilo_vida'])) {
                        $estudiante->actividad = $respuestasJson['actividades_estilo_vida'];
                    }
                }

                // Opción B: Buscar en la relación cuestionario o estiloVida
                if (empty($estudiante->actividad)) {
                    $estudiante->actividad = optional($estudiante->cuestionario)->actividad 
                        ?? optional($estudiante->estiloVida)->actividad 
                        ?? optional($estudiante->estiloVida)->descripcion;
                }
            }
        }

        // Cálculo de estadísticas para el dashboard
        $totalEstudiantes = $estudiantes->count();
        $riesgoAlto = $estudiantes->filter(fn($e) => $e->riesgo?->nivel_riesgo === 'Alto')->count();
        $riesgoMedio = $estudiantes->filter(fn($e) => $e->riesgo?->nivel_riesgo === 'Medio')->count();
        $conPsico = $estudiantes->filter(fn($e) => $e->orientacionPsicologica?->observaciones && $e->orientacionPsicologica->observaciones !== 'Sin orientación')->count();

        $statsEstudiantes = [
            'total_estudiantes'    => $totalEstudiantes,
            'riesgo_alto'          => $riesgoAlto,
            'riesgo_medio'         => $riesgoMedio,
            'con_psicoorientacion' => $conPsico,
        ];

        return view('dashboard', compact('estudiantes', 'statsEstudiantes', 'searchTerm'));
    }

    // Muestra el formulario de edición
    public function edit($codigo_estudiante)
    {
        $estudiante = Estudiante::with(['programa', 'directorUnidad', 'riesgo', 'orientacionPsicologica', 'estiloVida', 'saberesPrevios'])
            ->where('codigo_estudiante', $codigo_estudiante)
            ->firstOrFail();

        $programas = DB::table('programas_academicos')->get();
        $directores = DirectorUnidad::all(); 

        return view('estudiantes.edit', compact('estudiante', 'programas', 'directores'));
    }

    public function update(Request $request, $codigo_estudiante)
    {
        $estudiante = Estudiante::where('codigo_estudiante', $codigo_estudiante)->firstOrFail();

        // 1. Validamos los datos
        $request->validate([
            'id_programa'       => 'required',
            'nombre_estudiante' => 'required|string',
            'correo'            => 'required|email',
            'jornada'           => 'required|string',
            'actividad'         => 'nullable|string',
        ]);

        // 2. Definimos el mapeo de programa a director
        $mapeoDirectores = [
            1 => 1, // Programa ID 1 (Ingeniería) -> Director ID 1
            2 => 3, // Programa ID 2 (Agropecuaria) -> Director ID 3
            3 => 2  // Programa ID 3 (Contaduría) -> Director ID 2
        ];

        // 3. Obtenemos el nuevo director
        $nuevoIdDirector = $mapeoDirectores[$request->id_programa] ?? $estudiante->id_director_unidad;

        // 4. Actualizamos la información base del estudiante
        $estudiante->update([
            'nombre_estudiante'       => $request->nombre_estudiante,
            'correo'                  => $request->correo,
            'id_programa'             => $request->id_programa,
            'id_director_unidad'      => $nuevoIdDirector,
            'jornada'                 => $request->jornada,
            'actividades_estilo_vida' => $request->actividad,
        ]);

        // Actualización opcional de la relación Cuestionario o EstiloVida si aplica
        if ($estudiante->cuestionario) {
            $estudiante->cuestionario->update(['actividad' => $request->actividad]);
        }

        // Redirige al monitoreo de alertas para evitar bloqueos si el psicólogo no usa el dashboard general
        return redirect()->route('alertas.monitoreo')->with('success', 'Estudiante actualizado correctamente.');
    }

    // Elimina el registro (Solo Administrador según las rutas)
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