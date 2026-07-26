<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\ProgramaAcademico;
use App\Models\RiesgoDesercion;
use App\Models\User;
use App\Models\OrientacionPsicologica;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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

        // Precarga de relaciones
        $query = Estudiante::with([
            'user',
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

        // Aplicar Filtros (Búsqueda, Programa, Semestre, Jornada y Género)
        $query = $this->aplicarFiltros($query, $request);

        $estudiantes = $query->paginate(15)
                             ->appends($request->query());

        $programas = ProgramaAcademico::all();

        return view('alertas.monitoreo', compact('estudiantes', 'programas'));
    }

    public function exportPdf(Request $request)
    {
        $user = auth()->user();

        $query = Estudiante::with([
            'user',
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

        $query = $this->aplicarFiltros($query, $request);

        $estudiantes = $query->get();

        $pdf = Pdf::loadView('pdf.monitoreo', compact('estudiantes'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('reporte-monitoreo-estudiantes-' . date('Y-m-d') . '.pdf');
    }

    private function aplicarFiltros($query, Request $request)
    {
        return $query
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $buscar = $request->input('buscar');
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('codigo_estudiante', 'like', "%{$buscar}%")
                        ->orWhere('nombre_estudiante', 'like', "%{$buscar}%")
                        ->orWhereHas('user', function ($u) use ($buscar) {
                            $u->where('username', 'like', "%{$buscar}%")
                              ->orWhere('name', 'like', "%{$buscar}%")
                              ->orWhere('email', 'like', "%{$buscar}%");
                        });
                });
            })
            ->when($request->filled('programa'), function ($q) use ($request) {
                $q->where('id_programa', $request->input('programa'));
            })
            ->when($request->filled('semestre'), function ($q) use ($request) {
                $q->whereHas('saberesPrevios', function ($s) use ($request) {
                    $s->where('semestre', $request->input('semestre'));
                });
            })
            ->when($request->filled('jornada'), function ($q) use ($request) {
                $q->where('jornada', $request->input('jornada'));
            })
            ->when($request->filled('genero'), function ($q) use ($request) {
                $q->where('genero', $request->input('genero'));
            });
    }
}