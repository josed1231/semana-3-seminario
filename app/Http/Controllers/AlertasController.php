<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\ProgramaAcademico;
use App\Models\RiesgoDesercion;
use App\Models\OrientacionPsicologica;
use Illuminate\Http\Request;

class AlertasController extends Controller
{
    /**
     * Muestra las estadísticas y gráficos institucionales (Dashboard)
     */
    public function dashboard()
    {
        $statsEstudiantes = [
            'total_estudiantes'    => Estudiante::count(),
            'riesgo_alto'          => RiesgoDesercion::where('nivel_riesgo', 'Alto')->count(),
            'riesgo_medio'         => RiesgoDesercion::where('nivel_riesgo', 'Medio')->count(),
            'riesgo_bajo'          => RiesgoDesercion::where('nivel_riesgo', 'Bajo')->count(),
            'con_psicoorientacion' => OrientacionPsicologica::count(),
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
            'programa', 
            'directorUnidad', 
            'riesgo', 
            'orientacionPsicologica', 
            'saberesPrevios',
            'estiloVida'
        ]);

        // Restricción por rol: Si es Director de Unidad, filtra solo sus estudiantes asignados por programa
        if ($user && $user->rol === 'dir_unidad') {
            $map = [
                'dir_ingenieria'   => 1, 
                'dir_agropecuaria' => 2, 
                'dir_contaduria'   => 3
            ];
            if (isset($map[$user->username])) {
                $query->where('id_programa', $map[$user->username]);
            }
        }

        // Aplicar Scopes de búsqueda y filtrado
        $estudiantes = $query->buscar($request->input('buscar'))
                             ->filtrarPrograma($request->input('programa'))
                             ->filtrarSemestre($request->input('semestre'))
                             ->filtrarJornada($request->input('jornada'))
                             ->paginate(15)
                             ->appends($request->query()); // Mantiene los filtros en los enlaces de paginación

        // Obtener programas para cargar en el select de la vista
        $programas = ProgramaAcademico::all();

        return view('alertas.monitoreo', compact('estudiantes', 'programas'));
    }
}