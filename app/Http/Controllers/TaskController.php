<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['user', 'category']);

        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }

        if ($request->filled('estado')) {
            if ($request->estado == 'completada') {
                $query->completadas();
            } else {
                $query->pendientes();
            }
        }

        $tareas = $query->orderBy('created_at', 'desc')->get();

        return view('tasks.index', compact('tareas'));
    }

    public function create()
    {
        $categorias = Category::all();
        return view('tasks.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'fecha_limite' => 'nullable|date',
            'category_id' => 'required|exists:categories,id',
        ]);

        $data = $request->all();
        $data['user_id'] = 1; // Temporal, mientras no hay autenticación

        Task::create($data);

        return redirect()->route('tasks.index')->with('success', 'Tarea creada exitosamente.');
    }

    public function edit(Task $task)
    {
        $categorias = Category::all();
        $tarea = $task;
        return view('tasks.edit', compact('tarea', 'categorias'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
        'titulo' => 'required|string|max:150',
        'descripcion' => 'nullable|string',
        'fecha_limite' => 'nullable|date',
        'category_id' => 'required|exists:categories,id',
        'estado' => 'required|in:pendiente,en_progreso,completado',
        ]);
    $task->update($request->all());
    return redirect()->route('tasks.index')->with('success', 'Tarea actualizada exitosamente.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Tarea eliminada.');
    }
    
}