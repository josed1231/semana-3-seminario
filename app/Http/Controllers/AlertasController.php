<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\ProgramaAcademico;
use App\Models\RiesgoDesercion;
use App\Models\User;
use App\Models\OrientacionPsicologica;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // 👈 Importar Fachada de DomPDF

class AlertasController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        $queryEstudiantes = Estudiante::query();
        $queryRiesgos = RiesgoDesercion::query();
        $queryOrientaciones = OrientacionPsicologica::query();

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

    /**
     * Exportar el listado completo de Monitoreo a PDF respetando filtros y rol
     */
    public function exportPdf(Request $request)
    {
        $user = auth()->user();

        $query = Estudiante::with([
            'programa.directorUnidad', 
            'riesgo', 
            'orientacionPsicologica', 
            'saberesPrevios',
            'estiloVida'
        ]);

        // Si es Director de Unidad, filtra únicamente sus programas
        if ($user && $user->rol === 'dir_unidad') {
            $programasDelDirector = ProgramaAcademico::where('id_docente', $user->id)->pluck('id_programa');
            $query->whereIn('id_programa', $programasDelDirector);
        }

        // Aplica EXACTAMENTE los mismos filtros de la vista Web
        $estudiantes = $query->buscar($request->input('buscar'))
                             ->filtrarPrograma($request->input('programa'))
                             ->filtrarSemestre($request->input('semestre'))
                             ->filtrarJornada($request->input('jornada'))
                             ->get(); // Se usa get() para traer todo el resultado filtrado sin paginación

        // Cargar vista PDF en formato horizontal (landscape) para tablas anchas
        $pdf = Pdf::loadView('pdf.monitoreo', compact('estudiantes'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('reporte-monitoreo-estudiantes-' . date('Y-m-d') . '.pdf');
    }
}