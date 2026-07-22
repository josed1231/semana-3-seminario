<?php

namespace App\Http\Controllers;

use App\Models\ProgramaAcademico;
use App\Models\DirectorUnidad;
use App\Models\Estudiante;
use App\Models\User; // <-- Importación agregada
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programas = ProgramaAcademico::with('directorUnidad')->get();
        // Carga directamente los usuarios con rol de director de unidad desde la tabla users
        $directores = User::where('rol', 'dir_unidad')->get();

        return view('programas.index', compact('programas', 'directores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_programa' => 'required|string|max:255',
            'id_docente'      => 'nullable|exists:users,id',
        ]);

        ProgramaAcademico::create([
            'nombre_programa' => $request->nombre_programa,
            'id_docente'      => $request->id_docente ?: null,
        ]);

        return redirect()->route('programas.index')->with('success', 'Programa académico creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $programa = ProgramaAcademico::findOrFail($id);

        $docenteId = $request->input('id_docente') 
                ?? $request->input('id_director') 
                ?? $request->input('id_director_unidad');

        if ($request->has('nombre_programa')) {
            $programa->nombre_programa = $request->nombre_programa;
        }

        if ($docenteId !== null) {
            $programa->id_docente = $docenteId ?: null;
        }

        $programa->save();

        // Sincroniza la actualización con los estudiantes del programa si aplica
        if ($docenteId) {
            Estudiante::where('id_programa', $programa->id_programa)
                ->update(['id_docente' => $docenteId]);
        }

        return redirect()->route('programas.index')->with('success', 'Programa y Director actualizados correctamente.');
    }

    public function destroy($id)
    {
        $programa = ProgramaAcademico::findOrFail($id);
        $programa->delete();

        return redirect()->route('programas.index')->with('success', 'Programa académico eliminado correctamente.');
    }
}