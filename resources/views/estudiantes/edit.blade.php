<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Editar Estudiante: ') }} <span class="text-emerald-700">{{ $estudiante->nombre_estudiante ?? $estudiante->nombre ?? 'Estudiante' }}</span>
            </h2>
            <div class="flex gap-2">
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                    Código: {{ $estudiante->codigo_estudiante ?? 'N/A' }}
                </span>
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Cédula: {{ data_get($estudiante, 'user.username', data_get($estudiante, 'username', 'N/A')) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-gradient-to-tr from-stone-50 via-green-50/30 to-orange-50/20 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl rounded-3xl p-6 md:p-10 border border-emerald-100/60">

                @php
                    // Roles y Permisos del Usuario
                    $user = auth()->user();
                    $userRol = $user->rol ?? $user->role ?? '';
                    $esAdmin = $userRol === 'admin';
                    $esPsicologo = in_array($userRol, ['psicologo', 'bienestar', 'psicologia']);
                    $esDirectivo = in_array($userRol, ['dir_bienestar', 'dir_unidad', 'director']);

                    // Permisos de edición por sección
                    $puedeEditarAcademico = $esAdmin || $esDirectivo;
                    $puedeEditarOrientacion = $esAdmin || $esPsicologo;

                    // Valor dinámico de estilo de vida
                    $actividadValor = old('actividad', 
                        data_get($estudiante, 'actividades_estilo_vida') 
                        ?? data_get($estudiante, 'cuestionario.actividad') 
                        ?? data_get($estudiante, 'estiloVida.actividades_estilo_vida')
                    );

                    // Nivel de Riesgo y Detalles
                    $riesgoActual = old('nivel_riesgo', data_get($estudiante, 'riesgo.nivel_riesgo', data_get($estudiante, 'nivel_riesgo', 'Bajo')));
                    $detallesRiesgo = old('detalles', data_get($estudiante, 'riesgo.detalles', data_get($estudiante, 'detalles')));

                    // Orientación Psicológica
                    $nivelServicioActual = old('nivel_servicio', data_get($estudiante, 'orientacionPsicologica.nivel_servicio', 'Tutoría Académica Standard'));
                    $observacionesActual = old('observaciones', data_get($estudiante, 'orientacionPsicologica.observaciones'));

                    // Datos Académicos
                    $semestreActual = old('semestre', data_get($estudiante, 'semestre', data_get($estudiante, 'cuestionario.semestre', 1)));
                    $trabajaActual = old('trabaja', data_get($estudiante, 'trabaja', data_get($estudiante, 'cuestionario.trabaja', 'No')));
                    $generoActual = old('genero', data_get($estudiante, 'genero', 'Masculino'));

                    // Rutas seguras
                    $codigoEst = data_get($estudiante, 'codigo_estudiante', data_get($estudiante, 'id', 1));
                    $routeUpdate = Route::has('estudiantes.update') ? route('estudiantes.update', $codigoEst) : '#';
                    $routeCancelar = Route::has('alertas.monitoreo') 
                        ? route('alertas.monitoreo') 
                        : (Route::has('estudiantes.index') ? route('estudiantes.index') : url()->previous());
                @endphp

                <!-- Alertas de éxito o error del sistema -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-2xl text-sm font-medium flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-800 rounded-2xl text-sm font-medium flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Formulario principal -->
                <form id="editEstudianteForm" action="{{ $routeUpdate }}" method="POST" class="space-y-8" onsubmit="prepararEnvio()"
                      data-puede-academico="{{ $puedeEditarAcademico ? '1' : '0' }}"
                      data-puede-orientacion="{{ $puedeEditarOrientacion ? '1' : '0' }}"
                      data-es-admin="{{ $esAdmin ? '1' : '0' }}">
                    @csrf
                    @method('PUT')

                    <!-- SECCIÓN 1: DATOS ACADÉMICOS E INSTITUCIONALES -->
                    <div class="bg-emerald-50/40 p-6 md:p-8 rounded-2xl border border-emerald-100/80 space-y-6">
                        <div class="flex items-center justify-between border-b border-emerald-100 pb-3">
                            <h3 class="text-lg font-bold text-emerald-950 flex items-center gap-2">
                                🎓 Datos Académicos e Institucionales
                            </h3>
                            @if(!$puedeEditarAcademico)
                                <span class="text-xs bg-amber-100 text-amber-800 font-medium px-2.5 py-0.5 rounded-full border border-amber-200">
                                    Lectura protegida
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Código del Estudiante -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Código del Estudiante</label>
                                <input type="text" value="{{ data_get($estudiante, 'codigo_estudiante') }}" disabled class="block w-full rounded-xl border-gray-300 bg-gray-100/70 text-gray-500 text-sm cursor-not-allowed shadow-sm py-2.5">
                            </div>

                            <!-- Cédula (username) -->
                            <div class="space-y-2">
                                <label for="cedula" class="block text-sm font-semibold text-gray-700">Cédula</label>
                                <input type="text" id="cedula" name="cedula" value="{{ old('cedula', data_get($estudiante, 'user.username', data_get($estudiante, 'username'))) }}" 
                                       {{ !$puedeEditarAcademico ? 'readonly' : '' }}
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                                @error('cedula') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Correo Institucional -->
                            <div class="space-y-2">
                                <label for="correo" class="block text-sm font-semibold text-gray-700">Correo Institucional <span class="text-red-500">*</span></label>
                                <input type="email" id="correo" name="correo" value="{{ old('correo', data_get($estudiante, 'correo', data_get($estudiante, 'user.email'))) }}" required 
                                       {{ !$puedeEditarAcademico ? 'readonly' : '' }}
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                                @error('correo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Nombre Completo -->
                        <div class="space-y-2">
                            <label for="nombre_estudiante" class="block text-sm font-semibold text-gray-700">Nombre Completo <span class="text-red-500">*</span></label>
                            <input type="text" id="nombre_estudiante" name="nombre_estudiante" value="{{ old('nombre_estudiante', data_get($estudiante, 'nombre_estudiante', data_get($estudiante, 'user.name'))) }}" required 
                                   {{ !$puedeEditarAcademico ? 'readonly' : '' }}
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                            @error('nombre_estudiante') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Programa Académico -->
                            <div class="space-y-2">
                                <label for="id_programa" class="block text-sm font-semibold text-gray-700">Programa Académico <span class="text-red-500">*</span></label>
                                <select id="id_programa" name="id_programa" required {{ !$puedeEditarAcademico ? 'disabled' : '' }}
                                        class="permiso-academico block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                                    <option value="" disabled>-- Seleccione un Programa --</option>
                                    @foreach($programas ?? [] as $prog)
                                        @php
                                            $progId = data_get($prog, 'id_programa', data_get($prog, 'id'));
                                            $progNombre = data_get($prog, 'nombre_programa', data_get($prog, 'nombre'));
                                        @endphp
                                        <option value="{{ $progId }}" {{ old('id_programa', data_get($estudiante, 'id_programa')) == $progId ? 'selected' : '' }}>
                                            {{ $progNombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(!$puedeEditarAcademico)
                                    <input type="hidden" name="id_programa" value="{{ old('id_programa', data_get($estudiante, 'id_programa')) }}">
                                @endif
                                @error('id_programa') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Director de Unidad (Asignación Automática) -->
                            <div class="space-y-2">
                                <label for="id_director_unidad_visual" class="block text-sm font-semibold text-gray-700">Director de Unidad Asignado</label>
                                <select id="id_director_unidad_visual" disabled 
                                        class="block w-full rounded-xl border-gray-300 bg-gray-100/80 shadow-sm text-gray-600 text-sm py-2.5 cursor-not-allowed font-medium">
                                    <option value="" disabled selected>-- Asignación Automática --</option>
                                    @foreach($directores ?? [] as $director)
                                        @php 
                                            $dirId = data_get($director, 'id_director_unidad', data_get($director, 'id', data_get($director, 'id_usuario'))); 
                                            $dirNombre = data_get($director, 'nombre_director', data_get($director, 'nombre', data_get($director, 'nombre_completo'))); 
                                        @endphp
                                        <option value="{{ $dirId }}" {{ old('id_director_unidad', old('id_docente', data_get($estudiante, 'id_docente'))) == $dirId ? 'selected' : '' }}>
                                            {{ $dirNombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="id_director_unidad" name="id_director_unidad" value="{{ old('id_director_unidad', data_get($estudiante, 'id_docente')) }}">
                                <input type="hidden" id="id_docente" name="id_docente" value="{{ old('id_docente', data_get($estudiante, 'id_docente')) }}">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Semestre -->
                            <div class="space-y-2">
                                <label for="semestre" class="block text-sm font-semibold text-gray-700">Semestre</label>
                                <select id="semestre" name="semestre" {{ !$puedeEditarAcademico ? 'disabled' : '' }} 
                                        class="permiso-academico block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ $semestreActual == $i ? 'selected' : '' }}>Semestre {{ $i }}</option>
                                    @endfor
                                </select>
                                @if(!$puedeEditarAcademico)
                                    <input type="hidden" name="semestre" value="{{ $semestreActual }}">
                                @endif
                            </div>

                            <!-- Jornada -->
                            <div class="space-y-2">
                                <label for="jornada" class="block text-sm font-semibold text-gray-700">Jornada <span class="text-red-500">*</span></label>
                                <select id="jornada" name="jornada" required {{ !$puedeEditarAcademico ? 'disabled' : '' }}
                                        class="permiso-academico block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                                    <option value="Diurna" {{ old('jornada', data_get($estudiante, 'jornada')) == 'Diurna' ? 'selected' : '' }}>Diurna</option>
                                    <option value="Nocturna" {{ old('jornada', data_get($estudiante, 'jornada')) == 'Nocturna' ? 'selected' : '' }}>Nocturna</option>
                                    <option value="Sabatina" {{ old('jornada', data_get($estudiante, 'jornada')) == 'Sabatina' ? 'selected' : '' }}>Sabatina</option>
                                </select>
                                @if(!$puedeEditarAcademico)
                                    <input type="hidden" name="jornada" value="{{ old('jornada', data_get($estudiante, 'jornada')) }}">
                                @endif
                                @error('jornada') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- ¿Trabaja? -->
                            <div class="space-y-2">
                                <label for="trabaja" class="block text-sm font-semibold text-gray-700">¿Trabaja Actualmente?</label>
                                <select id="trabaja" name="trabaja" {{ !$puedeEditarAcademico ? 'disabled' : '' }}
                                        class="permiso-academico block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                                    <option value="Si" {{ $trabajaActual == 'Si' ? 'selected' : '' }}>Sí</option>
                                    <option value="No" {{ $trabajaActual == 'No' ? 'selected' : '' }}>No</option>
                                </select>
                                @if(!$puedeEditarAcademico)
                                    <input type="hidden" name="trabaja" value="{{ $trabajaActual }}">
                                @endif
                            </div>

                            <!-- Género -->
                            <div class="space-y-2">
                                <label for="genero" class="block text-sm font-semibold text-gray-700">Género <span class="text-red-500">*</span></label>
                                <select id="genero" name="genero" required {{ !$puedeEditarAcademico ? 'disabled' : '' }}
                                        class="permiso-academico block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                                    <option value="Masculino" {{ $generoActual == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Femenino" {{ $generoActual == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="No binario" {{ $generoActual == 'No binario' ? 'selected' : '' }}>No binario</option>
                                    <option value="Otro" {{ $generoActual == 'Otro' ? 'selected' : '' }}>Otro / Prefiero no decir</option>
                                </select>
                                @if(!$puedeEditarAcademico)
                                    <input type="hidden" name="genero" value="{{ $generoActual }}">
                                @endif
                                @error('genero') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: ANÁLISIS DE RIESGO Y ESTILOS DE VIDA -->
                    <div class="p-6 md:p-8 bg-white rounded-2xl border border-amber-200/80 shadow-sm space-y-6">
                        <div class="flex items-center justify-between border-b border-amber-100 pb-3">
                            <h3 class="text-lg font-bold text-amber-800 flex items-center gap-2">
                                ⚠️ Análisis de Riesgo y Estilos de Vida
                            </h3>
                            <span class="px-3 py-1 text-xs font-bold rounded-full 
                                {{ $riesgoActual === 'Alto' ? 'bg-red-100 text-red-800 border border-red-200' : '' }}
                                {{ $riesgoActual === 'Medio' ? 'bg-amber-100 text-amber-800 border border-amber-200' : '' }}
                                {{ $riesgoActual === 'Bajo' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : '' }}">
                                Riesgo {{ $riesgoActual }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nivel de Riesgo -->
                            <div class="space-y-2">
                                <label for="nivel_riesgo" class="block text-sm font-semibold text-gray-700">Nivel de Riesgo Evaluado</label>
                                <select id="nivel_riesgo" name="nivel_riesgo" {{ !$esAdmin ? 'disabled' : '' }}
                                        class="permiso-admin block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$esAdmin ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                                    <option value="Bajo" {{ $riesgoActual == 'Bajo' ? 'selected' : '' }}>Bajo</option>
                                    <option value="Medio" {{ $riesgoActual == 'Medio' ? 'selected' : '' }}>Medio</option>
                                    <option value="Alto" {{ $riesgoActual == 'Alto' ? 'selected' : '' }}>Alto</option>
                                </select>
                                @if(!$esAdmin)
                                    <input type="hidden" name="nivel_riesgo" value="{{ $riesgoActual }}">
                                @endif
                            </div>

                            <!-- Actividades Frecuentes -->
                            <div class="space-y-2">
                                <label for="actividad" class="block text-sm font-semibold text-gray-700">Actividades (Estilo de Vida)</label>
                                <input type="text" id="actividad" name="actividad" value="{{ $actividadValor }}"
                                       placeholder="Actividades extracurriculares o hábitos..."
                                       {{ !$puedeEditarAcademico ? 'readonly' : '' }}
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                            </div>
                        </div>

                        <!-- Detalles Adicionales -->
                        <div class="space-y-2">
                            <label for="detalles" class="block text-sm font-semibold text-gray-700">Detalles Adicionales del Riesgo</label>
                            <textarea id="detalles" name="detalles" rows="2" 
                                      {{ !$esAdmin ? 'readonly' : '' }}
                                      placeholder="Anotaciones técnicas del nivel de deserción..."
                                      class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$esAdmin ? 'bg-gray-50 text-gray-500' : 'text-gray-900 bg-white' }} text-sm">{{ $detallesRiesgo }}</textarea>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: ORIENTACIÓN PSICOLÓGICA Y ACOMPAÑAMIENTO PIAE -->
                    <div class="p-6 md:p-8 bg-white rounded-2xl border border-indigo-100 shadow-sm space-y-6">
                        <div class="flex items-center justify-between border-b border-indigo-100 pb-3">
                            <h3 class="text-lg font-bold text-indigo-900 flex items-center gap-2">
                                💼 Orientación Psicológica y Acompañamiento (PIAE)
                            </h3>
                            @if($puedeEditarOrientacion)
                                <span class="text-xs bg-indigo-100 text-indigo-800 font-semibold px-2.5 py-0.5 rounded-full">
                                    Edición Habilitada (Bienestar/Admin)
                                </span>
                            @else
                                <span class="text-xs bg-slate-100 text-slate-600 font-medium px-2.5 py-0.5 rounded-full flex items-center gap-1">
                                    🔒 Solo lectura
                                </span>
                            @endif
                        </div>

                        <!-- Nivel de Servicio Asignado -->
                        <div class="space-y-2">
                            <label for="nivel_servicio" class="block text-sm font-semibold text-gray-700">
                                Nivel de Servicio Asignado (Generación Automática / Manual)
                            </label>
                            @if($puedeEditarOrientacion)
                                <select id="nivel_servicio" name="nivel_servicio" class="permiso-orientacion block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 text-gray-900 bg-white text-sm py-2.5">
                                    <option value="Tutoría Académica Standard" {{ $nivelServicioActual === 'Tutoría Académica Standard' ? 'selected' : '' }}>
                                        Tutoría Académica Standard
                                    </option>
                                    <option value="Acompañamiento Psicoeducativo Preventivo" {{ $nivelServicioActual === 'Acompañamiento Psicoeducativo Preventivo' ? 'selected' : '' }}>
                                        Acompañamiento Psicoeducativo Preventivo
                                    </option>
                                    <option value="Atención Prioritaria Bienestar / Psicología" {{ $nivelServicioActual === 'Atención Prioritaria Bienestar / Psicología' ? 'selected' : '' }}>
                                        Atención Prioritaria Bienestar / Psicología
                                    </option>
                                </select>
                            @else
                                <input type="text" id="nivel_servicio" value="{{ $nivelServicioActual }}" readonly class="block w-full rounded-xl border-gray-300 bg-slate-50 text-slate-700 font-medium text-sm py-2.5">
                                <input type="hidden" name="nivel_servicio" value="{{ $nivelServicioActual }}">
                            @endif
                        </div>

                        <!-- Observaciones -->
                        <div class="space-y-2">
                            <label for="observaciones" class="block text-sm font-semibold text-gray-700">
                                Observaciones, Diagnóstico y Ruta de Atención
                            </label>
                            <textarea id="observaciones" name="observaciones" rows="5" 
                                      {{ !$puedeEditarOrientacion ? 'readonly' : '' }}
                                      placeholder="El sistema asigna la ruta de atención automáticamente, pero el equipo de Bienestar puede ajustar o complementar las recomendaciones aquí..."
                                      class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 {{ !$puedeEditarOrientacion ? 'bg-slate-50 text-slate-600' : 'text-gray-900 bg-white' }} text-sm">{{ $observacionesActual }}</textarea>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <a href="{{ $routeCancelar }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-bold transition shadow-sm border border-gray-200">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-[#f17a28] hover:bg-[#d66213] text-white px-7 py-2.5 rounded-xl text-sm font-bold transition shadow-md flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Guardar Cambios
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const programmeSelect = document.getElementById('id_programa');
            const directorSelectVisual = document.getElementById('id_director_unidad_visual');
            const directorInputHidden = document.getElementById('id_director_unidad');
            const docenteInputHidden = document.getElementById('id_docente');

            function actualizarDirector() {
                if (!programmeSelect || !directorSelectVisual || !directorInputHidden) return;

                const selectedOption = programmeSelect.options[programmeSelect.selectedIndex];
                if (!selectedOption) return;

                const programaTexto = selectedOption.text.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                let palabrasClave = [];

                if (programaTexto.includes('sistemas') || programaTexto.includes('ingenier')) {
                    palabrasClave = ['sistemas', 'ingenier'];
                } else if (programaTexto.includes('contad') || programaTexto.includes('publica')) {
                    palabrasClave = ['contad'];
                } else if (programaTexto.includes('agro') || programaTexto.includes('pecuaria')) {
                    palabrasClave = ['agro'];
                }

                if (palabrasClave.length > 0) {
                    for (let i = 0; i < directorSelectVisual.options.length; i++) {
                        const opcionTexto = directorSelectVisual.options[i].text.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                        const coincide = palabrasClave.some(clave => opcionTexto.includes(clave));
                        if (coincide) {
                            directorSelectVisual.selectedIndex = i;
                            const valorDirector = directorSelectVisual.options[i].value;
                            
                            directorInputHidden.value = valorDirector;
                            if (docenteInputHidden) {
                                docenteInputHidden.value = valorDirector;
                            }
                            return;
                        }
                    }
                }
            }

            if (programmeSelect) {
                programmeSelect.addEventListener('change', actualizarDirector);
                actualizarDirector();
            }
        });

        function prepararEnvio() {
            const formulario = document.getElementById('editEstudianteForm');
            if (!formulario) return;
            
            const puedeEditarAcademico = formulario.dataset.puedeAcademico === '1';
            const puedeEditarOrientacion = formulario.dataset.puedeOrientacion === '1';
            const esAdmin = formulario.dataset.esAdmin === '1';

            if (puedeEditarAcademico) {
                formulario.querySelectorAll('.permiso-academico[disabled]').forEach(function (elemento) {
                    elemento.removeAttribute('disabled');
                });
            }

            if (puedeEditarOrientacion) {
                formulario.querySelectorAll('.permiso-orientacion[disabled]').forEach(function (elemento) {
                    elemento.removeAttribute('disabled');
                });
            }

            if (esAdmin) {
                formulario.querySelectorAll('.permiso-admin[disabled]').forEach(function (elemento) {
                    elemento.removeAttribute('disabled');
                });
            }
        }
    </script>
</x-app-layout>