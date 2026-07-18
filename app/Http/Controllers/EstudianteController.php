<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DirectorUnidad;
use App\Models\Estudiante;

class EstudianteController extends Controller
{
    // Listado de estudiantes y estadísticas del Dashboard
    public function index(Request $request)
    {
        $user = auth()->user();
        $rol = $user->rol;
        $searchTerm = $request->input('search');

        // Carga de relaciones necesarias para el dashboard
        $query = Estudiante::with(['programa', 'directorUnidad', 'riesgo', 'orientacionPsicologica', 'estiloVida', 'saberesPrevios']);

        // Filtro por unidad académica para directores
        if ($rol === 'dir_unidad') {
            $map = ['dir_ingenieria' => 1, 'dir_agropecuaria' => 2, 'dir_contaduria' => 3];
            $query->where('id_programa', $map[$user->username] ?? 0);
        }

        // Buscador por nombre o código
        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombre_estudiante', 'like', "%$searchTerm%")
                  ->orWhere('codigo_estudiante', 'like', "%$searchTerm%");
            });
        }

        $estudiantes = $query->get();

        // Cálculo de estadísticas para el dashboard
        $totalEstudiantes = $estudiantes->count();
        $riesgoAlto = $estudiantes->filter(fn($e) => $e->riesgo?->nivel_riesgo === 'Alto')->count();
        $riesgoMedio = $estudiantes->filter(fn($e) => $e->riesgo?->nivel_riesgo === 'Medio')->count();
        $conPsico = $estudiantes->filter(fn($e) => $e->orientacionPsicologica?->observaciones && $e->orientacionPsicologica->observaciones !== 'Sin orientación')->count();

        $statsEstudiantes = [
            'total_estudiantes' => $totalEstudiantes,
            'riesgo_alto'       => $riesgoAlto,
            'riesgo_medio'      => $riesgoMedio,
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

    // Actualiza la información del estudiante
    public function update(Request $request, $codigo_estudiante)
    {
        $request->validate([
            'nombre_estudiante'  => 'required|string|max:255',
            'correo'             => 'required|email',
            'id_programa'        => 'required',
            'id_director_unidad' => 'required', 
            'promedio'           => 'required|numeric|min:0|max:5',
            'semestre'           => 'required|integer|min:1|max:10',
            'jornada'            => 'required|string',
            'nivel_riesgo'       => 'nullable|string',
            'detalles'           => 'nullable|string',
        ]);

        $estudiante = Estudiante::where('codigo_estudiante', $codigo_estudiante)->firstOrFail();

        $estudiante->update([
            'nombre_estudiante' => $request->nombre_estudiante,
            'correo'            => $request->correo,
            'id_programa'       => $request->id_programa,
            'id_director'       => $request->id_director_unidad, 
            'promedio'          => $request->promedio,
            'jornada'           => $request->jornada,
        ]);

        DB::table('saberes_previos')->where('codigo_estudiante', $codigo_estudiante)->update([
            'semestre'   => $request->semestre,
            'updated_at' => now(),
        ]);

        $estudiante->riesgo()->updateOrCreate(
            ['codigo_estudiante' => $estudiante->codigo_estudiante],
            [
                'nivel_riesgo' => $request->nivel_riesgo,
                'detalles'     => $request->detalles,
            ]
        );

        return redirect()->route('dashboard')->with('success', 'Estudiante actualizado correctamente.');
    }

    // Elimina el registro
    public function destroy($codigo_estudiante)
    {
        try {
            $estudiante = Estudiante::where('codigo_estudiante', $codigo_estudiante)->firstOrFail();
            $estudiante->delete();
            return redirect()->route('dashboard')->with('success', 'Estudiante eliminado.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Error al eliminar.');
        }
    }
}