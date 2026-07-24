<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgramaAcademico;
use Illuminate\Http\Request;

class ProgramaController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_programa' => 'required|string|max:255',
            'id_docente'      => 'nullable|exists:users,id', // Correcto: valida contra la tabla users
        ]);

        $programa = ProgramaAcademico::create([
            'nombre_programa' => $validated['nombre_programa'],
            'id_docente'      => $validated['id_docente'] ?? null,
        ]);

        return response()->json([
            'message' => 'Programa creado exitosamente',
            'data'    => $programa
        ], 201);
    }
}