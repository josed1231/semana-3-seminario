<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User; 

class PsicologoController extends Controller
{
    public function edit($codigo)
    {
        // Obtener datos del estudiante y su riesgo
        $estudiante = DB::table('estudiantes')->where('codigo_estudiante', $codigo)->first();
        $riesgo = DB::table('riesgos_desercion')->where('codigo_estudiante', $codigo)->first();

        return view('psicologo.editar_observacion', compact('estudiante', 'riesgo'));
    }

    public function update(Request $request, $codigo)
    {
        $request->validate([
            'observacion_psicologo' => 'required|string|max:1000',
        ]);

        // SOLO se actualiza la observación
        DB::table('riesgos_desercion')
            ->where('codigo_estudiante', $codigo)
            ->update([
                'observacion_psicologo' => $request->input('observacion_psicologo'),
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Observación actualizada correctamente.');
    }

    public function index()
    {
        // Aquí iría la lógica para mostrar la lista de estudiantes al psicólogo
        return view('psicologo.index');
    }
    
}
