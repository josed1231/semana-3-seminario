<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstudianteController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validar incluyendo 'promedio'
        $validated = $request->validate([
            'codigo_estudiante' => 'required|string|unique:estudiantes,codigo_estudiante|max:255',
            'nombre_estudiante' => 'required|string|max:255',
            'jornada'           => 'required|string|max:255',
            'correo'            => 'required|email|max:255',
            'id_programa'       => 'required|integer',
            'id_director'       => 'required|integer',
            'id_docente'        => 'required|integer',
            'promedio'          => 'required|numeric|min:0|max:5', // Agrega esto
        ]);

        // 2. Crear el estudiante
        $estudiante = Estudiante::create($validated);

        // ... (el resto de tu código para saberes_previos sigue igual)
        // app/Http/Controllers/Api/EstudianteController.php

        DB::table('saberes_previos')->insert([
            'codigo_estudiante' => $estudiante->codigo_estudiante,
            'semestre'          => 1,
            'respuestas'        => 'valor_inicial', // <-- DEBES AGREGAR ESTO
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return response()->json(['message' => 'Estudiante creado', 'data' => $estudiante], 201);
    }
}