<?php

namespace App\Http\Controllers;

use App\Models\DirectorUnidad;
use App\Models\ProgramaAcademico;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Http\Request;

class DirectorUnidadController extends Controller
{
    /**
     * Muestra el listado de directores de unidad, sus programas asignados
     * y los usuarios con rol 'dir_unidad'.
     */
    public function index()
    {
        // Carga los directores con sus programas asociados directamente
        $directores = User::where('rol', 'dir_unidad')->get();

        // Obtiene todos los programas académicos
        $programas = ProgramaAcademico::all();

        // Trae los usuarios cuyo rol sea 'dir_unidad' o similar
        $usuariosDirectores = User::where('rol', 'dir_unidad')->get();

        return view('directores.index', compact('directores', 'programas'));
    }

    /**
     * Registra un nuevo Director de Unidad.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_director' => 'required|string|max:255',
            'correo_director' => 'required|email|unique:directores_unidad,correo_director',
        ]);

        DirectorUnidad::create([
            'nombre_director' => $request->nombre_director,
            'correo_director' => $request->correo_director,
        ]);

        return redirect()->route('directores.index')->with('success', 'Director de Unidad creado exitosamente.');
    }

    /**
     * Actualiza la información de un Director de Unidad.
     */
    public function update(Request $request, $id)
    {
        $director = DirectorUnidad::findOrFail($id);

        $request->validate([
            'nombre_director' => 'required|string|max:255',
            'correo_director' => 'required|email|unique:directores_unidad,correo_director,' . $id . ',id_docente',
        ]);

        $director->update([
            'nombre_director' => $request->nombre_director,
            'correo_director' => $request->correo_director,
        ]);

        return redirect()->route('directores.index')->with('success', 'Director de Unidad actualizado correctamente.');
    }

    /**
     * Elimina un Director de Unidad y limpia sus referencias.
     */
    public function destroy($id)
    {
        $director = DirectorUnidad::findOrFail($id);

        // 1. Desvincular los programas asociados directamente
        ProgramaAcademico::where('id_docente', $id)->update(['id_docente' => null]);

        // 2. Desvincular los estudiantes asociados
        Estudiante::where('id_docente', $id)->update(['id_docente' => null]);

        // 3. Eliminar el registro del director
        $director->delete();

        return redirect()->route('directores.index')->with('success', 'Director de Unidad eliminado correctamente.');
    }
}