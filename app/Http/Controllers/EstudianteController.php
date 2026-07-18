<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Estudiante;

class EstudianteController extends Controller
{
    // Muestra el formulario de registro
    public function create()
    {
        $programas = DB::table('programas_academicos')->get();
        // Nota: Asegúrate que la tabla en DB se llame 'docentes' o cambia el nombre
        $docentes = DB::table('docentes')->get(); 

        return view('estudiantes.create', compact('programas', 'docentes'));
    }

    // Muestra el formulario de edición
    public function edit($codigo_estudiante)
    {
        // Se cambió 'docente' por 'directorUnidad' para coincidir con tu modelo
        $estudiante = Estudiante::with(['programa', 'directorUnidad', 'riesgo', 'orientacionPsicologica', 'estiloVida'])
            ->where('codigo_estudiante', $codigo_estudiante)
            ->firstOrFail();

        $programas = DB::table('programas_academicos')->get();
        $docentes = DB::table('docentes')->get();

        return view('estudiantes.edit', compact('estudiante', 'programas', 'docentes'));
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $rol = $user->rol;
        $searchTerm = $request->input('search');

        // 1. CORRECCIÓN: Se cambió 'docente' por 'directorUnidad' aquí
        $query = Estudiante::with(['programa', 'directorUnidad', 'riesgo', 'orientacionPsicologica', 'estiloVida']);

        // 2. Filtro por unidad
        if ($rol === 'dir_unidad') {
            $map = ['dir_ingenieria' => 1, 'dir_agropecuaria' => 2, 'dir_contaduria' => 3];
            $query->where('id_programa', $map[$user->username] ?? 0);
        }

        // 3. Buscador
        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombre_estudiante', 'like', "%$searchTerm%")
                  ->orWhere('codigo_estudiante', 'like', "%$searchTerm%");
            });
        }

        $estudiantes = $query->get();

        // 4. CALCULAR ESTADÍSTICAS (Asegurando que las llaves coincidan con el dashboard)
        $totalEstudiantes = $estudiantes->count();
        $riesgoAlto = $estudiantes->filter(fn($e) => $e->riesgo?->nivel_riesgo === 'Alto')->count();
        $riesgoMedio = $estudiantes->filter(fn($e) => $e->riesgo?->nivel_riesgo === 'Medio')->count();
        $conPsico = $estudiantes->filter(fn($e) => $e->orientacionPsicologica?->observaciones && $e->orientacionPsicologica->observaciones !== 'Sin orientación')->count();

        // 5. Array explícito para evitar el error de "Undefined array key"
        $statsEstudiantes = [
            'total_estudiantes' => $totalEstudiantes,
            'riesgo_alto'       => $riesgoAlto,
            'riesgo_medio'      => $riesgoMedio,
            'con_psicoorientacion' => $conPsico,
        ];

        return view('dashboard', compact('estudiantes', 'statsEstudiantes', 'searchTerm'));
    }

    // ... mantener store, update y destroy igual, 
    // pero asegurando cambiar 'id_docente' por 'id_director_unidad' en los validadores
}