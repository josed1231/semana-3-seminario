<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Programa; // Asegúrate de que el modelo esté en App\Models
use Illuminate\Http\Request;

class ProgramaController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_programa' => 'required|string|max:255',
        ]);

        $programa = Programa::create($validated);

        return response()->json([
            'message' => 'Programa creado exitosamente',
            'data' => $programa
        ], 201);
    }
}