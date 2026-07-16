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
        if (!Schema::hasTable('programas_academicos')) {
            return "Error: No se encontró ninguna tabla de programas ('programas_academicos') en tu base de datos. Por favor, corre las migraciones.";
        }

        $programas = DB::table('programas_academicos')->get();$docentes = DB::table('docentes')->get();

        return view('estudiantes.create', compact('programas', 'docentes'));
    }

    // Muestra el formulario de edición
    public function edit($codigo_estudiante)
    {
        // SEGURIDAD: Bloquear el acceso si es Director de Unidad (docente) o Psicólogo (si no debe editar datos académicos)
        if (!auth()->user()->rol === 'admin' && !auth()->user()->rol === 'psicologo') {
        abort(403, 'No tienes permisos para acceder a la edición.');
        }

        if (!Schema::hasTable('programas_academicos')) {
            return "Error: No se encontró ninguna tabla de programas en la base de datos.";
        }

        $estudiante = Estudiante::with(['programa', 'docente', 'riesgo', 'orientacionPsicologica', 'estiloVida'])
            ->where('codigo_estudiante', $codigo_estudiante)
            ->firstOrFail();

        $programas = DB::table('programas_academicos')->get();
        $docentes = DB::table('docentes')->get();

        return view('estudiantes.edit', compact('estudiante', 'programas', 'docentes'));
    }

    // Guarda el estudiante en la base de datos
    public function store(Request $request)
    {
        // SEGURIDAD: Evitar que roles no autorizados guarden registros
        if (in_array(auth()->user()->rol, ['dir_unidad', 'psicologo'])) {
            abort(403, 'No tienes permisos para registrar estudiantes.');
        }

        $request->validate([
            'codigo_estudiante' => 'required|string|max:50|unique:estudiantes,codigo_estudiante',
            'nombre_estudiante' => 'required|string|max:255',
            'correo'            => 'required|email|max:255',
            'id_programa'       => 'required|integer',
            'id_docente'        => 'required|integer',
            'promedio'          => 'required|numeric|between:0,5.0',
        ]);

        // PROTECCIÓN SQLi: Usamos Eloquent (que usa sentencias preparadas por defecto bajo PDO)
        Estudiante::create([
            'codigo_estudiante' => $request->input('codigo_estudiante'),
            'nombre_estudiante' => $request->input('nombre_estudiante'),
            'correo'            => $request->input('correo'),
            'id_programa'       => $request->input('id_programa'),
            'id_docente'        => $request->input('id_docente'),
            'promedio'          => $request->input('promedio'),
        ]);

        return redirect()->route('dashboard')->with('success', 'Estudiante registrado con éxito.');
    }

    public function update(Request $request, $codigo_estudiante)
    {
        // SEGURIDAD: Bloquear el acceso si es Director de Unidad (docente)
        if (auth()->user()->rol === 'dir_unidad') {
            abort(403, 'No tienes permisos para actualizar estudiantes.');
        }

        $estudiante = Estudiante::where('codigo_estudiante', $codigo_estudiante)->firstOrFail();
        $rol = auth()->user()->rol;

        // ACTUALIZAR DATOS ACADÉMICOS (Solo si es Admin o Bienestar, NO si es psicologo)
        if ($rol !== 'psicologo') {
            $request->validate([
                'nombre_estudiante' => 'required|string|max:255',
                'correo' => 'required|email|max:255',
                'id_programa' => 'required|integer',
                'id_docente' => 'required|integer',
                'promedio' => 'required|numeric|between:0,5.0',
            ]);

            $estudiante->update([
                'nombre_estudiante' => $request->nombre_estudiante,
                'correo' => $request->correo,
                'id_programa' => $request->id_programa,
                'id_docente' => $request->id_docente,
                'promedio' => $request->promedio,
            ]);

            // PROTECCIÓN SQLi: updateOrInsert parametrizado de Laravel
            DB::table('riesgos_desercion')->updateOrInsert(
                ['codigo_estudiante' => $estudiante->codigo_estudiante],
                [
                    'nivel_riesgo' => $request->input('nivel_riesgo'),
                    'detalles' => $request->input('detalles'),
                    'updated_at' => now(),
                ]
            );

            DB::table('estilos_vida')->updateOrInsert(
                ['codigo_estudiante' => $estudiante->codigo_estudiante],
                [
                    'horas_estudio_semanal' => $request->input('horas_estudio_semanal'),
                    'trabaja' => $request->input('trabaja'),
                    'updated_at' => now(),
                ]
            );
        }

        // ACTUALIZAR ORIENTACIÓN PSICOPEDAGÓGICA (Solo si es psicologo o admin)
        if (!in_array($rol, ['dir_bienestar', 'dir_unidad'])) {
            DB::table('orientaciones_psicologicas')->updateOrInsert(
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
        // Bloquear la eliminación si es Director de Unidad (docente)
        if (auth()->user()->rol === 'dir_unidad') {
            abort(403, 'No tienes permisos para eliminar estudiantes.');
        }

        $estudiante = Estudiante::where('codigo_estudiante', $codigo_estudiante)->firstOrFail();$estudiante->delete();

        return redirect()->route('dashboard')->with('success', 'Estudiante eliminado correctamente.');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $rol = $user->rol;
        $searchTerm = $request->input('search');

        // 1. Iniciamos la consulta base de estudiantes
        $query = Estudiante::with(['programa', 'docente', 'riesgo', 'orientacionPsicologica', 'estiloVida']);

        // 2. FILTRO ESTRICTO DE SEGURIDAD POR UNIDAD ACADÉMICA
        // Esto restringe la query base, asegurando que ni el buscador ni las estadísticas accedan a otras carreras
        if ($rol === 'dir_unidad') {
            if ($user->username === 'dir_ingenieria') {
                $query->where('id_programa', 1); // Filtra estrictamente Ingeniería
            } elseif ($user->username === 'dir_agropecuaria') {
                $query->where('id_programa', 2); // Filtra estrictamente Agropecuaria
            } elseif ($user->username === 'dir_contaduria') {
                $query->where('id_programa', 3); // Filtra estrictamente Contaduría
            } else {
                // Si por alguna razón tiene el rol pero no un username reconocido, no le mostramos nada por seguridad
                $query->whereRaw('1 = 0');
            }
        }

        // 3. APLICAR BUSCADOR (Si hay un término, buscará solo sobre los estudiantes ya filtrados arriba)
        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombre_estudiante', 'like', '%' . $searchTerm . '%')
                  ->orWhere('codigo_estudiante', 'like', '%' . $searchTerm . '%');
            });
        }

        // 4. OBTENER LOS ESTUDIANTES PERMITIDOS
        $estudiantes = $query->get();

        // 5. CALCULAR ESTADÍSTICAS DINÁMICAS (Solo con los estudiantes de su propia carrera)
        $totalEstudiantes = $estudiantes->count();
        $riesgoAlto = 0;
        $riesgoMedio = 0;
        $conPsico = 0;

        foreach ($estudiantes as $est) {
            if ($est->riesgo) {
                if ($est->riesgo->nivel_riesgo === 'Alto') {
                    $riesgoAlto++;
                } elseif ($est->riesgo->nivel_riesgo === 'Medio') {
                    $riesgoMedio++;
                }
            }
            if ($est->orientacionPsicologica && !empty($est->orientacionPsicologica->observaciones) && $est->orientacionPsicologica->observaciones !== 'Sin orientación') {
                $conPsico++;
            }
        }

        $statsEstudiantes = [
            'total_estudiantes' => $totalEstudiantes,
            'riesgo_alto'       => $riesgoAlto,
            'riesgo_medio'      => $riesgoMedio,
            'con_psicoorientacion' => $conPsico,
        ];

        return view('dashboard', compact('estudiantes', 'statsEstudiantes', 'searchTerm'));
    }
}