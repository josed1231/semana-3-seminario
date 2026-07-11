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
            if ($request->estado == 'completado') {
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
        $data['user_id'] = 1;

        Task::create($data);

        return redirect()->route('tasks.index')->with('success', 'Tarea creada exitosamente.');
    }

    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    public function edit($id)
    {
        // 1. Buscamos la tarea por su ID o lanzamos un error 404 si no existe
        $tarea = Task::findOrFail($id);

        // 2. CRUCIAL: Traer todas las categorías de la base de datos para el select
        $categories = Category::all(); 

        // 3. Enviamos ambas variables de forma estricta a la vista
        return view('tasks.edit', compact('tarea', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $tarea = Task::findOrFail($id);
        
        // Aquí va tu lógica de validación y guardado ($tarea->update(...))
        $tarea->titulo = $request->input('titulo');
        $tarea->descripcion = $request->input('descripcion');
        $tarea->category_id = $request->input('category_id');
        $tarea->estado = $request->input('estado');
        $tarea->fecha_limite = $request->input('fecha_limite');
        $tarea->save();

        // CORRECCIÓN: Cambiar la redirección explícita hacia el dashboard
        return redirect()->to('/dashboard')->with('success', 'Tarea actualizada exitosamente.');
    }

    // En tu TaskController.php
    public function destroy($id)
    {
        $tarea = Task::findOrFail($id);
        $tarea->delete();

        // CRUCIAL: Esto obliga a Laravel a recargar el Dashboard con los datos frescos
        return redirect()->to('/dashboard')->with('success', 'Tarea eliminada correctamente.');
    }
}