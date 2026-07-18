<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RiesgoDesercion; // Asegúrate de que el modelo esté en App\Models
use Illuminate\Http\Request;

class RiesgoDesercionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_estudiante' => 'required|string|max:255',
            'nivel_riesgo'      => 'required|in:Pendiente,Bajo,Medio,Alto', // Ajusta según los valores de tu ENUM
            'detalles'          => 'nullable|string',
        ]);

        $riesgo = RiesgoDesercion::create($validated);

        return response()->json([
            'message' => 'Registro de riesgo creado exitosamente',
            'data' => $riesgo
        ], 201);
    }
}