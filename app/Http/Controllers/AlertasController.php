<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\ProgramaAcademico;
use App\Models\RiesgoDesercion;
use App\Models\User;
use App\Models\OrientacionPsicologica;
use Illuminate\Http\Request;

class AlertasController extends Controller
{
    /**
     * Muestra las estadísticas y gráficos institucionales (Dashboard)
     * Adaptado para filtrar por Director de Unidad si aplica.
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        $queryEstudiantes = Estudiante::query();
        $queryRiesgos = RiesgoDesercion::query();
        $queryOrientaciones = OrientacionPsicologica::query();

        // Filtrado por Director de Unidad si el rol es dir_unidad
        if ($user && $user->rol === 'dir_unidad') {
            $programasDelDirector = ProgramaAcademico::where('id_docente', $user->id)->pluck('id_programa');
            $codigosEstudiantes = Estudiante::whereIn('id_programa', $programasDelDirector)->pluck('codigo_estudiante');

            $queryEstudiantes->whereIn('id_programa', $programasDelDirector);
            $queryRiesgos->whereIn('codigo_estudiante', $codigosEstudiantes);
            $queryOrientaciones->whereIn('codigo_estudiante', $codigosEstudiantes);
        }

        $statsEstudiantes = [
            'total_estudiantes'    => $queryEstudiantes->count(),
            'riesgo_alto'          => (clone $queryRiesgos)->where('nivel_riesgo', 'Alto')->count(),
            'riesgo_medio'         => (clone $queryRiesgos)->where('nivel_riesgo', 'Medio')->count(),
            'riesgo_bajo'          => (clone $queryRiesgos)->where('nivel_riesgo', 'Bajo')->count(),
            'con_psicoorientacion' => $queryOrientaciones->count(),
        ];

        return view('dashboard', compact('statsEstudiantes'));
    }

    /**
     * Módulo operativo con el listado, filtrado y búsqueda de estudiantes (Monitoreo)
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Estudiante::with([
            'programa.directorUnidad', 
            'riesgo', 
            'orientacionPsicologica', 
            'saberesPrevios',
            'estiloVida'
        ]);

        if ($user && $user->rol === 'dir_unidad') {
            $programasDelDirector = ProgramaAcademico::where('id_docente', $user->id)->pluck('id_programa');
            $query->whereIn('id_programa', $programasDelDirector);
        }

        // Aplicar Scopes
        $estudiantes = $query->buscar($request->input('buscar'))
                             ->filtrarPrograma($request->input('programa'))
                             ->filtrarSemestre($request->input('semestre'))
                             ->filtrarJornada($request->input('jornada'))
                             ->paginate(15)
                             ->appends($request->query());

        $programas = ProgramaAcademico::all();

        return view('alertas.monitoreo', compact('estudiantes', 'programas'));
    }
}