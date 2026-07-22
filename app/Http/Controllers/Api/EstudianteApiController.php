<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Estudiante;
use App\Models\ProgramaAcademico;

class EstudianteApiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $rol = $user->rol ?? 'user';

        $query = Estudiante::with([
            'programa', 
            'directorUnidad', 
            'riesgo', 
            'orientacionPsicologica', 
            'estiloVida', 
            'saberesPrevios'
        ]);

        if ($rol === 'dir_unidad') {
            $programasDelDirector = ProgramaAcademico::where('id_docente', $user->id)->pluck('id_programa');
            $query->whereIn('id_programa', $programasDelDirector);
        }

        if ($searchTerm = $request->input('search')) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombre_estudiante', 'like', "%{$searchTerm}%")
                  ->orWhere('codigo_estudiante', 'like', "%{$searchTerm}%");
            });
        }

        $estudiantes = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $estudiantes
        ], 200);
    }

    public function show($codigo)
    {
        $estudiante = Estudiante::with([
            'programa', 
            'directorUnidad', 
            'riesgo', 
            'orientacionPsicologica', 
            'estiloVida', 
            'saberesPrevios'
        ])->where('codigo_estudiante', $codigo)->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $estudiante
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_estudiante' => 'required|string|unique:estudiantes,codigo_estudiante',
            'nombre_estudiante' => 'required|string|max:255',
            'id_programa'       => 'required|exists:programas_academicos,id_programa',
            'jornada'           => 'required|string',
            'promedio'          => 'nullable|numeric',
        ]);

        $programa = ProgramaAcademico::findOrFail($request->id_programa);

        $estudiante = Estudiante::create([
            'codigo_estudiante'       => $request->codigo_estudiante,
            'nombre_estudiante'       => $request->nombre_estudiante,
            'id_programa'             => $request->id_programa,
            'id_docente'              => $programa->id_docente,
            'jornada'                 => $request->jornada,
            'promedio'                => $request->promedio ?? 0,
            'actividades_estilo_vida' => $request->input('actividades_estilo_vida'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estudiante creado exitosamente',
            'data'    => $estudiante
        ], 201);
    }
}