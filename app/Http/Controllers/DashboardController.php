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
        $searchTerm = $request->input('search');

        // 2. Traemos estudiantes con la relación 'directorUnidad' (antes 'docente')
        $estudiantes = Estudiante::with(['programa', 'directorUnidad', 'riesgo', 'orientacionPsicologica', 'estiloVida'])
            ->when($searchTerm, function ($query, $searchTerm) {
                return $query->where('nombre_estudiante', 'LIKE', "%{$searchTerm}%")
                             ->orWhere('codigo_estudiante', 'LIKE', "%{$searchTerm}%");
            })
            ->get();

        // 3. Estadísticas (sin cambios en la lógica de conteo)
        $statsEstudiantes = [
            'total_estudiantes'    => Estudiante::count(),
            'riesgo_alto'          => RiesgoDesercion::where('nivel_riesgo', 'Alto')->count(),
            'riesgo_medio'         => RiesgoDesercion::where('nivel_riesgo', 'Medio')->count(),
            'riesgo_bajo'          => RiesgoDesercion::where('nivel_riesgo', 'Bajo')->count(),
            'con_psicoorientacion' => OrientacionPsicologica::count(),
        ];

        $statsTareas = [
            'total'       => Task::count(),
            'completadas' => Task::where('estado', 'completado')->count(),
            'en_progreso' => Task::where('estado', 'en_progreso')->count(),
            'pendientes'  => Task::where('estado', 'pendiente')->count(),
        ];

        return view('dashboard', compact('estudiantes', 'statsEstudiantes', 'statsTareas', 'searchTerm'));
    }
}