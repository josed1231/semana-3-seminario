<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Estudiante;
use App\Models\RiesgoDesercion;
use App\Models\OrientacionPsicologica;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Capturamos la búsqueda si el usuario quiere filtrar estudiantes
        $searchTerm = $request->input('search');

        // 2. Traemos los estudiantes con sus relaciones (¡Descripciones en lugar de IDs!)
        $estudiantes = Estudiante::with(['programa', 'docente', 'riesgo', 'orientacionPsicologica', 'estiloVida'])
            ->when($searchTerm, function ($query, $searchTerm) {
                return $query->where('nombre_estudiante', 'LIKE', "%{$searchTerm}%")
                             ->orWhere('codigo_estudiante', 'LIKE', "%{$searchTerm}%");
            })
            ->get();

        // 3. Calculamos estadísticas reales basadas en tu Base de Datos para el Dashboard
        $statsEstudiantes = [
            'total_estudiantes'    => Estudiante::count(),
            'riesgo_alto'          => RiesgoDesercion::where('nivel_riesgo', 'Alto')->count(),
            'riesgo_medio'         => RiesgoDesercion::where('nivel_riesgo', 'Medio')->count(),
            'riesgo_bajo'          => RiesgoDesercion::where('nivel_riesgo', 'Bajo')->count(),
            'con_psicoorientacion' => OrientacionPsicologica::count(),
        ];

        // 4. Tus estadísticas de tareas (mantenemos lo que ya tenías)
        $statsTareas = [
            'total'       => Task::count(),
            'completadas' => Task::where('estado', 'completado')->count(),
            'en_progreso' => Task::where('estado', 'en_progreso')->count(),
            'pendientes'  => Task::where('estado', 'pendiente')->count(),
        ];

        // 5. Retornamos la vista con todas las variables
        return view('dashboard', compact('estudiantes', 'statsEstudiantes', 'statsTareas', 'searchTerm'));
    }
}