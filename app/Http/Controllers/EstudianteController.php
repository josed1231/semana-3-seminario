<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// IMPORTA TUS MODELOS AQUÍ:
use App\Models\Estudiante;
use App\Models\Riesgo; 
use App\Models\EstiloVida;

class EstudianteController extends Controller
{
    // Muestra el formulario de registro reutilizando tu vista física 'tasks.create'

    public function create()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('programas_academicos')) {
            return "Error: No se encontró ninguna tabla de programas ('programas_academicos') en tu base de datos. Por favor, corre las migraciones.";
        }

        $programas = \Illuminate\Support\Facades\DB::table('programas_academicos')->get();
        $docentes = \Illuminate\Support\Facades\DB::table('docentes')->get();

        return view('estudiantes.create', compact('programas', 'docentes'));
    }

    public function edit($codigo_estudiante)
    {
        // Cambiar cualquier variante de nombre por 'programas_academicos'
        if (!\Illuminate\Support\Facades\Schema::hasTable('programas_academicos')) {
            return "Error: No se encontró ninguna tabla de programas en la base de datos.";
        }

        // Consulta corregida con todas las relaciones bien escritas y cerradas
        $estudiante = Estudiante::with(['programa', 'docente', 'riesgo', 'orientacionPsicologica', 'estiloVida'])
            ->where('codigo_estudiante', $codigo_estudiante)
            ->firstOrFail();

        $programas = \Illuminate\Support\Facades\DB::table('programas_academicos')->get();
        $docentes = \Illuminate\Support\Facades\DB::table('docentes')->get();

        return view('estudiantes.edit', compact('estudiante', 'programas', 'docentes'));
    }

    // Guarda el estudiante en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'codigo_estudiante' => 'required',
            'nombre_estudiante' => 'required|string|max:255',
            'correo'            => 'required|email',
            'id_programa'       => 'required',
            'id_docente'        => 'required',
            'promedio'          => 'required|numeric|between:0,5.0',
        ]);

        // Guardar registro de forma segura en la tabla 'estudiantes'
        DB::table('estudiantes')->insert([
            'codigo_estudiante' => $request->input('codigo_estudiante'),
            'nombre_estudiante' => $request->input('nombre_estudiante'),
            'correo'            => $request->input('correo'),
            'id_programa'       => $request->input('id_programa'),
            'id_docente'        => $request->input('id_docente'),
            'promedio'          => $request->input('promedio'),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Estudiante registrado con éxito.');
    }

    public function update(Request $request, $codigo_estudiante)
    {
        // 1. Buscar el estudiante
        $estudiante = Estudiante::findOrFail($codigo_estudiante);

        // 2. Obtener el rol del usuario logueado
        $rol = auth()->user()->rol;

        // 3. ACTUALIZAR DATOS ACADÉMICOS (Solo si NO es psicologo)
        if ($rol !== 'psicologo') {
            $request->validate([
                'nombre_estudiante' => 'required|string|max:255',
                'correo' => 'required|email|max:255',
                'id_programa' => 'required',
                'id_docente' => 'required',
                'promedio' => 'required|numeric|between:0,5.0',
            ]);

            // Actualizar datos de la tabla estudiantes
            $estudiante->update([
                'nombre_estudiante' => $request->nombre_estudiante,
                'correo' => $request->correo,
                'id_programa' => $request->id_programa,
                'id_docente' => $request->id_docente,
                'promedio' => $request->promedio,
            ]);

            // Actualizar o insertar el Riesgo en la base de datos de manera directa
            \Illuminate\Support\Facades\DB::table('riesgos_desercion')->updateOrInsert(
                ['codigo_estudiante' => $estudiante->codigo_estudiante],
                [
                    'nivel_riesgo' => $request->input('nivel_riesgo'),
                    'detalles' => $request->input('detalles'),
                    'updated_at' => now(),
                ]
            );

            // Actualizar o insertar el Estilo de Vida de manera directa
            // Nota: Asegúrate de que 'estilos_vida' sea el nombre exacto de tu tabla en la base de datos
            \Illuminate\Support\Facades\DB::table('estilos_vida')->updateOrInsert(
                ['codigo_estudiante' => $estudiante->codigo_estudiante],
                [
                    'horas_estudio_semanal' => $request->input('horas_estudio_semanal'),
                    'trabaja' => $request->input('trabaja'),
                    'updated_at' => now(),
                ]
            );
        }

        // 4. ACTUALIZAR ORIENTACIÓN PSICOPEDAGÓGICA (Solo si NO es dir_bienestar ni dir_unidad)
        if (!in_array($rol, ['dir_bienestar', 'dir_unidad'])) {
            \Illuminate\Support\Facades\DB::table('orientaciones_psicologicas')->updateOrInsert(
                ['codigo_estudiante' => $estudiante->codigo_estudiante],
                [
                    'observaciones' => $request->input('observaciones'),
                    'updated_at' => now(),
                ]
            );
        }

        return redirect()->route('dashboard')->with('success', 'Estudiante actualizado correctamente.');
    }

    public function destroy($codigo_estudiante)
    {
        // Buscamos el estudiante por su código único
        $estudiante = Estudiante::where('codigo_estudiante',$codigo_estudiante)->firstOrFail();
        
        // Al eliminarlo, gracias a la regla 'onDelete(cascade)' que pusimos en las migraciones,
        // se borrarán automáticamente sus riesgos, estilos de vida y orientaciones asociadas.
        $estudiante->delete();

        return redirect()->route('dashboard')->with('success', 'Estudiante eliminado correctamente.');
    }

}