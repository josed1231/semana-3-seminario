<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task; 

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tareas = Task::with(['category', 'user'])->get();
        return response()->json($tareas, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'fecha_limite' => 'nullable|date|after:today',
            'category_id' => 'required|exists:categories,id',
        ]);

        $tarea = Task::create([
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
            'fecha_limite' => $validated['fecha_limite'] ?? null,
            'category_id' => $validated['category_id'],
            'user_id' => auth('api')->id(),
            'estado' => 'pendiente',
        ]);

        return response()->json($tarea, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $task = Task::with(['category', 'user'])->findOrFail($id);
        return response()->json($task, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Obtén la tarea con las relaciones (opcional)
            $task = Task::findOrFail($id);

        // Validación (sin cambios)
            $validated = $request->validate([
                'titulo' => 'sometimes|string|max:150',
                'descripcion' => 'nullable|string',
                'fecha_limite' => 'nullable|date|after:today',
                'category_id' => 'sometimes|exists:categories,id',
                'estado' => 'sometimes|in:pendiente,en_progreso,completada',
            ]);

        // (Opcional) Verifica autorización manualmente
            $user = auth('api')->user();
            $isAdmin = $user && $user->rol === 'admin';
            if (! $isAdmin && $user->id !== $task->user_id) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $task->update($validated);

            return response()->json($task->fresh()->load(['category', 'user']), 200);
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
            $task = Task::findOrFail($id);

        // (Opcional) Verifica autorización
            $user = auth('api')->user();
            $isAdmin = $user && $user->rol === 'admin';
            if (! $isAdmin && $user->id !== $task->user_id) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $task->delete();

            return response()->json(null, 204);
    }
}
