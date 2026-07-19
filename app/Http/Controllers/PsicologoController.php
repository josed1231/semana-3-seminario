<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Estudiante;

class PsicologoController extends Controller
{
    // Muestra la vista inicial de búsqueda
    public function index()
    {
        return view('resultados.index');
    }

    // Lógica para buscar al estudiante y sus respuestas (Compatible con Tiempo Real)
    public function buscar(Request $request)
    {
        $term = $request->input('codigo'); 

        // Si la petición viene desde AlpineJS pidiendo un JSON para las sugerencias de autocompletado:
        if ($request->wantsJson() || $request->ajax()) {
            if (empty($term)) {
                return response()->json([]);
            }

            // Buscamos coincidencias rápidas por código o nombre_estudiante
            $sugerencias = Estudiante::where('codigo_estudiante', 'LIKE', "%{$term}%")
                ->orWhere('nombre_estudiante', 'LIKE', "%{$term}%")
                ->take(6) 
                ->get(['codigo_estudiante', 'nombre_estudiante']);

            return response()->json($sugerencias);
        }
        
        // --- LÓGICA TRADICIONAL (Al presionar Enter o dar clic en Buscar) ---
        if (empty($term)) {
            return redirect()->route('resultados.index')
                ->withErrors(['msg' => 'Por favor, ingrese un término de búsqueda.']);
        }

        // Buscamos estudiante evaluando coincidencia exacta por código o nombre_estudiante
        $estudiante = Estudiante::with('saberesPrevios')
            ->where('codigo_estudiante', $term)
            ->orWhere('nombre_estudiante', $term)
            ->first();

        // Si el estudiante no existe o no tiene respuestas registradas
        if (!$estudiante || !$estudiante->saberesPrevios) {
            // REMOVIDO ->withInput(): Al no enviarlo, el campo de texto se reiniciará 
            // automáticamente y quedará completamente en blanco tras el redireccionamiento.
            return redirect()->route('resultados.index')
                ->withErrors(['msg' => 'No se encontraron resultados para el criterio: ' . $term]);
        }

        // Decodificamos el JSON de respuestas si todo es correcto
        $respuestas = json_decode($estudiante->saberesPrevios->respuestas, true);

        return view('resultados.index', compact('estudiante', 'respuestas'));
    }

    public function edit($codigo)
    {
        $estudiante = DB::table('estudiantes')->where('codigo_estudiante', $codigo)->first();
        $riesgo = DB::table('riesgos_desercion')->where('codigo_estudiante', $codigo)->first();
        return view('psicologo.editar_observacion', compact('estudiante', 'riesgo'));
    }

    public function update(Request $request, $codigo)
    {
        $request->validate(['observacion_psicologo' => 'required|string|max:1000']);

        DB::table('riesgos_desercion')->where('codigo_estudiante', $codigo)->update([
            'observacion_psicologo' => $request->input('observacion_psicologo'),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Observación actualizada.');
    }
}