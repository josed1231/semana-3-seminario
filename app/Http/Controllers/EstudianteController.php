<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\ProgramaAcademico;
use App\Models\DirectorUnidad;
use App\Models\OrientacionPsicologica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

class EstudianteController extends Controller
{
    use AuthorizesRequests;

    /**
     * Muestra la información del estudiante para edición.
     */
    public function edit($codigo_estudiante)
    {
        $estudiante = Estudiante::with(['user', 'programa', 'riesgo', 'orientacionPsicologica', 'saberesPrevios'])
            ->where('codigo_estudiante', $codigo_estudiante)
            ->firstOrFail();

        // 🔒 Verificación de autorización con Policy
        $this->authorize('view', $estudiante);

        // Si la relación 'user' no se resolvió automáticamente por id_user en la BD,
        // busca el usuario por correo o cédula (username) y le asigna el id_user permanentemente.
        if (!$estudiante->relationLoaded('user') || !$estudiante->user) {
            $user = User::where('email', $estudiante->getRawOriginal('correo'))
                ->orWhere('username', $estudiante->getRawOriginal('cedula'))
                ->orWhere('username', $estudiante->codigo_estudiante)
                ->orWhere('name', $estudiante->getRawOriginal('nombre_estudiante'))
                ->first();
                
            if ($user) {
                // 🔑 Asigna y guarda id_user en la BD para reparar el registro desvinculado
                $estudiante->id_user = $user->id;
                $estudiante->save();

                $estudiante->setRelation('user', $user);
            }
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($estudiante);
        }

        $programas = ProgramaAcademico::all();
        $directores = DirectorUnidad::all();

        return view('estudiantes.edit', compact('estudiante', 'programas', 'directores'));
    }

    /**
     * Actualiza la información del estudiante.
     */
    public function update(Request $request, $codigo_estudiante)
    {
        $estudiante = Estudiante::with(['user', 'saberesPrevios'])
            ->where('codigo_estudiante', $codigo_estudiante)
            ->firstOrFail();

        // 🔒 Verificación de autorización con Policy
        $this->authorize('update', $estudiante);

        // Detección del usuario vinculado para ignorarlo en las reglas de validación unique
        $userVinculado = $estudiante->user;

        if (!$userVinculado) {
            $userVinculado = User::where('username', $request->input('cedula'))
                ->orWhere('email', $request->input('correo'))
                ->orWhere('email', $estudiante->getRawOriginal('correo'))
                ->first();
        }

        $userId = $userVinculado?->id;

        // Validación ignorando el ID del usuario actual en la tabla users
        $request->validate([
            'cedula' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($userId),
            ],
            'correo' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'nombre_estudiante' => 'required|string|max:255',
            'id_programa'       => 'required',
            'jornada'           => 'required',
            'genero'            => 'required',
        ], [
            'cedula.unique' => 'La cédula ingresada ya se encuentra registrada en el sistema por otro usuario.',
            'correo.unique' => 'El correo institucional ingresado ya está en uso por otra cuenta.',
        ]);

        try {
            DB::transaction(function () use ($request, $estudiante, $userVinculado) {
                // 1. Determinar Director / Docente asignado
                $nuevoIdDirector = $request->input('id_docente', $estudiante->id_docente);
                if ($request->filled('id_programa')) {
                    $programa = ProgramaAcademico::find($request->id_programa);
                    if ($programa && $programa->id_docente) {
                        $nuevoIdDirector = $programa->id_docente;
                    }
                }

                if ($nuevoIdDirector) {
                    $directorExiste = DirectorUnidad::where('id_docente', $nuevoIdDirector)->exists();
                    if (!$directorExiste) {
                        $nuevoIdDirector = DirectorUnidad::where('id_docente', $estudiante->id_docente)->exists() 
                            ? $estudiante->id_docente 
                            : DirectorUnidad::value('id_docente');
                    }
                }

                // 2. Sincronizar Cédula (username), Nombre y Correo en el Modelo User vinculado
                $usuario = $estudiante->user ?? $userVinculado;
                if ($usuario) {
                    if ($request->filled('nombre_estudiante')) {
                        $usuario->name = $request->input('nombre_estudiante');
                    }
                    if ($request->filled('correo')) {
                        $usuario->email = $request->input('correo');
                    }
                    if ($request->filled('cedula')) {
                        $usuario->username = $request->input('cedula'); // Cédula guardada en users.username
                    }
                    $usuario->save();
                }

                // 3. Persistir datos en la tabla estudiantes y vincular el id_user
                $estudiante->correo = $request->input('correo');
                $estudiante->nombre_estudiante = $request->input('nombre_estudiante');
                $estudiante->genero = $request->input('genero', $estudiante->genero);
                $estudiante->id_programa = $request->input('id_programa', $estudiante->id_programa);
                $estudiante->id_docente = $nuevoIdDirector;
                $estudiante->jornada = $request->input('jornada', $estudiante->jornada);

                // 🔑 Guarda permanentemente la relación del ID de usuario
                if ($usuario) {
                    $estudiante->id_user = $usuario->id;
                }

                if ($request->has('actividad') || $request->has('actividades_estilo_vida')) {
                    $estudiante->actividades_estilo_vida = $request->input('actividad', $request->input('actividades_estilo_vida', ''));
                }

                $estudiante->save();

                // 4. Saberes Previos
                if ($estudiante->saberesPrevios) {
                    $respuestas = is_string($estudiante->saberesPrevios->respuestas) 
                        ? json_decode($estudiante->saberesPrevios->respuestas, true) 
                        : ($estudiante->saberesPrevios->respuestas ?? []);

                    if (is_array($respuestas)) {
                        if ($request->filled('genero')) {
                            $respuestas['genero'] = $request->input('genero');
                        }
                        if ($request->has('semestre')) {
                            $respuestas['semestre'] = $request->input('semestre');
                        }
                        if ($request->has('trabaja')) {
                            $respuestas['trabaja'] = $request->input('trabaja');
                        }

                        $estudiante->saberesPrevios->update([
                            'respuestas' => json_encode($respuestas, JSON_UNESCAPED_UNICODE)
                        ]);
                    }
                }

                // 5. Riesgo
                if ($request->filled('nivel_riesgo')) {
                    $estudiante->riesgo()->updateOrCreate(
                        ['codigo_estudiante' => $estudiante->codigo_estudiante],
                        [
                            'nivel_riesgo' => $request->input('nivel_riesgo', 'Bajo'),
                            'detalles'     => $request->input('detalles', ''),
                        ]
                    );
                }

                // 6. Orientación Psicológica
                OrientacionPsicologica::updateOrCreate(
                    ['codigo_estudiante' => $estudiante->codigo_estudiante],
                    [
                        'nivel_servicio' => $request->input('nivel_servicio', 'Tutoría Académica Standard'),
                        'observaciones'  => $request->input('observaciones', ''),
                    ]
                );
            });

            return redirect()->route('alertas.monitoreo')->with('success', 'Estudiante actualizado correctamente.');

        } catch (AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al actualizar estudiante: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Elimina un estudiante.
     */
    public function destroy($codigo_estudiante)
    {
        try {
            $estudiante = Estudiante::where('codigo_estudiante', $codigo_estudiante)->firstOrFail();

            // 🔒 Verificación de autorización con Policy
            $this->authorize('delete', $estudiante);

            $estudiante->delete();

            return redirect()->route('alertas.monitoreo')->with('success', 'Estudiante eliminado correctamente.');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al eliminar estudiante: ' . $e->getMessage());
            return redirect()->route('alertas.monitoreo')->with('error', 'Error al eliminar el estudiante.');
        }
    }
}