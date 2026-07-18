<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CuestionarioController extends Controller
{
    // Mostrar el formulario al estudiante logueado
    public function show()
    {
        $user = auth()->user();
        // Intentamos buscar si ya existe un registro previo de este estudiante
        $estudiante = DB::table('estudiantes')->where('correo',$user->email)->first();

        return view('cuestionario', compact('estudiante'));
    }

    // Almacenar las respuestas y calcular el riesgo automáticamente
    public function store(Request $request)
    {
        $user = auth()->user();
        $semestre = intval($request->input('semestre'));

        // Validaciones de los campos clave de la encuesta
        $request->validate([
            'semestre' => 'required|integer|between:1,10',
            'id_programa' => 'required|integer',
            'afectacion_academico' => 'required|integer',
            'afectacion_socioeconomico' => 'required|integer',
            'afectacion_psicosocial' => 'required|integer',
        ]);

        // 1. Obtener o insertar al estudiante asociado a la cuenta de usuario
        $estudiante = DB::table('estudiantes')->where('correo',$user->email)->first();

        // app/Http/Controllers/CuestionarioController.php

        if (!$estudiante) {
            $codigoEstudiante = 'EST-' . sprintf('%05d', $user->id);
            DB::table('estudiantes')->insert([
            'codigo_estudiante' => $codigoEstudiante,
            'nombre_estudiante' => $user->name,
            'correo'            => $user->email,
            'id_programa'       => $request->input('id_programa'),
            'id_docente'        => 1,
            'promedio'          => 0.0,
            // Aquí asignamos la jornada que el usuario tiene definida en su perfil
            'jornada'           => $user->jornada ?? 'Diurna', 
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
        } else {
            $codigoEstudiante =$estudiante->codigo_estudiante;
            // Actualizamos el programa si cambió en el formulario
            DB::table('estudiantes')->where('codigo_estudiante', $codigoEstudiante)->update([
                'id_programa' => $request->input('id_programa'),
                'updated_at' => now()
            ]);
        }

        // 2. Cálculo matemático automatizado del riesgo basado en el Excel
        $puntosAcad = intval($request->input('afectacion_academico'));
        $puntosSocio = intval($request->input('afectacion_socioeconomico'));
        $puntosPsico = intval($request->input('afectacion_psicosocial'));

        $totalCriterios =$puntosAcad + $puntosSocio +$puntosPsico;

        // Umbrales de corte para el módulo de predicción
        if ($totalCriterios >= 6) {$nivelRiesgo = 'Alto';
        } elseif ($totalCriterios >= 3) {$nivelRiesgo = 'Medio';
        } else {
            $nivelRiesgo = 'Bajo';
        }

        // 3. Serializar y guardar todas las respuestas en formato JSON en `saberes_previos`
        $respuestasCompletas =$request->except(['_token', 'semestre', 'id_programa']);

        DB::table('saberes_previos')->updateOrInsert(
            ['codigo_estudiante' => $codigoEstudiante],
            [
                'semestre' => $semestre,
                'respuestas' => json_encode($respuestasCompletas),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 4. Inyectar los resultados calculados directamente en la tabla de alertas
        DB::table('riesgos_desercion')->updateOrInsert(
            ['codigo_estudiante' => $codigoEstudiante],
            [
                'nivel_riesgo' => $nivelRiesgo,
                'detalles' => "Generado automáticamente por el Cuestionario PIAE. Puntaje acumulado: $totalCriterios",
                'updated_at' => now(),
            ]
        );

        return redirect()->route('cuestionario.success');
    }
}