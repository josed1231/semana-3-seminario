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

    // Lógica para buscar al estudiante y sus respuestas
    public function buscar(Request $request)
    {
        $codigo = $request->input('codigo');
        
        // Buscamos estudiante y su relación con saberes_previos
        $estudiante = Estudiante::with('saberesPrevios')
            ->where('codigo_estudiante', $codigo)
            ->first();

        if (!$estudiante || !$estudiante->saberesPrevios) {
            return back()->withErrors(['msg' => 'No se encontraron resultados para el ID: ' . $codigo]);
        }

        // Decodificamos el JSON de respuestas
        $respuestas = json_decode($estudiante->saberesPrevios->respuestas, true);

        return view('resultados.index', compact('estudiante', 'respuestas'));
    }

    // Mantener tus métodos edit/update originales para la gestión de riesgos
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