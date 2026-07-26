<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use Illuminate\Http\Request;

class GeneroController extends Controller
{
    /**
     * Muestra la lista de géneros configurados (Vista Admin)
     */
    public function index(Request $request)
    {
        $query = Genero::query();

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        $generos = $query->orderBy('nombre', 'asc')->paginate(10);

        return view('generos.index', compact('generos'));
    }

    /**
     * Almacena un nuevo género
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:generos,nombre',
            'activo' => 'required|boolean',
        ], [
            'nombre.unique' => 'Este género ya se encuentra registrado.'
        ]);

        Genero::create([
            'nombre' => trim($request->nombre),
            'activo' => $request->boolean('activo'),
        ]);

        return redirect()->back()->with('success', 'Género registrado correctamente.');
    }

    /**
     * Actualiza el nombre y/o estado del género
     */
    public function update(Request $request, $id)
    {
        $genero = Genero::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:50|unique:generos,nombre,' . $genero->id,
            'activo' => 'required|boolean',
        ], [
            'nombre.unique' => 'Este género ya se encuentra registrado.'
        ]);

        $genero->update([
            'nombre' => trim($request->nombre),
            'activo' => $request->boolean('activo'),
        ]);

        return redirect()->back()->with('success', 'Género actualizado correctamente.');
    }

    /**
     * Activa o desactiva un género sin eliminarlo de la BD
     */
    public function toggleStatus($id)
    {
        $genero = Genero::findOrFail($id);
        $genero->activo = !$genero->activo;
        $genero->save();

        return redirect()->back()->with('success', 'Estado del género actualizado.');
    }

    /**
     * Eliminar un género si no está en uso
     */
    public function destroy($id)
    {
        $genero = Genero::findOrFail($id);
        $genero->delete();

        return redirect()->back()->with('success', 'Género eliminado correctamente.');
    }
}