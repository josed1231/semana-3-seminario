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
                    Cédula: {{ $estudiante->cedula }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-gradient-to-tr from-stone-50 via-green-50/30 to-orange-50/20 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl rounded-3xl p-6 md:p-10 border border-emerald-100/60">

                @php
                    // Helper seguro para obtener relaciones/propiedades sin disparar excepciones 500
                    $safeGet = function($target, $path, $default = null) {
                        if (!$target) return $default;
                        try {
                            return data_get($target, $path, $default);
                        } catch (\Throwable $e) {
                            return $default;
                        }
                    };

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
                        $safeGet($estudiante, 'cuestionario.actividad') 
                        ?? $safeGet($estudiante, 'actividades_estilo_vida') 
                        ?? $safeGet($estudiante, 'estiloVida.actividad')
                    );

                    // Nivel de Riesgo y Detalles
                    $riesgoActual = old('nivel_riesgo', $safeGet($estudiante, 'riesgo.nivel_riesgo', 'Bajo'));
                    $detallesRiesgo = old('detalles', $safeGet($estudiante, 'riesgo.detalles'));

                    // Orientación Psicológica
                    $nivelServicioActual = old('nivel_servicio', $safeGet($estudiante, 'orientacionPsicologica.nivel_servicio', 'Tutoría Académica Standard'));
                    $observacionesActual = old('observaciones', $safeGet($estudiante, 'orientacionPsicologica.observaciones'));

                    // Datos Académicos
                    $semestreActual = old('semestre', $safeGet($estudiante, 'cuestionario.semestre', $safeGet($estudiante, 'semestre', 1)));
                    $trabajaActual = old('trabaja', $safeGet($estudiante, 'cuestionario.trabaja', $safeGet($estudiante, 'trabaja', 'No')));

                    // Rutas seguras
                    $codigoEst = $safeGet($estudiante, 'codigo_estudiante', $safeGet($estudiante, 'id', 1));
                    $routeUpdate = Route::has('estudiantes.update') ? route('estudiantes.update', $codigoEst) : '#';
                    $routeCancelar = Route::has('alertas.monitoreo') 
                        ? route('alertas.monitoreo') 
                        : (Route::has('estudiantes.index') ? route('estudiantes.index') : url()->previous());
                @endphp

                <form id="editEstudianteForm" action="{{ $routeUpdate }}" method="POST" class="space-y-8" onsubmit="prepararEnvio()">
                    @csrf
                    @method('PUT')

                    <!-- ======================================================== -->
                    <!-- SECCIÓN 1: DATOS ACADÉMICOS E INSTITUCIONALES           -->
                    <!-- ======================================================== -->
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
                                <input type="text" value="{{ $safeGet($estudiante, 'codigo_estudiante') }}" disabled class="block w-full rounded-xl border-gray-300 bg-gray-100/70 text-gray-500 text-sm cursor-not-allowed shadow-sm py-2.5">
                            </div>

                            <!-- Cédula -->
                            <div class="space-y-2">
                                <label for="cedula" class="block text-sm font-semibold text-gray-700">Cédula</label>
                                <input type="text" id="cedula" name="cedula" value="{{ old('cedula', $estudiante->cedula) }}" 
                                       {{ !$puedeEditarAcademico ? 'readonly' : '' }}
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                                @error('cedula') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Correo Institucional -->
                            <div class="space-y-2">
                                <label for="correo" class="block text-sm font-semibold text-gray-700">Correo Institucional <span class="text-red-500">*</span></label>
                                <input type="email" id="correo" name="correo" value="{{ old('correo', $safeGet($estudiante, 'correo')) }}" required 
                                       {{ !$puedeEditarAcademico ? 'readonly' : '' }}
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                                @error('correo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Nombre Completo -->
                        <div class="space-y-2">
                            <label for="nombre_estudiante" class="block text-sm font-semibold text-gray-700">Nombre Completo <span class="text-red-500">*</span></label>
                            <input type="text" id="nombre_estudiante" name="nombre_estudiante" value="{{ old('nombre_estudiante', $safeGet($estudiante, 'nombre_estudiante')) }}" required 
                                   {{ !$puedeEditarAcademico ? 'readonly' : '' }}
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                            @error('nombre_estudiante') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Programa Académico -->
                            <div class="space-y-2">
                                <label for="id_programa" class="block text-sm font-semibold text-gray-700">Programa Académico <span class="text-red-500">*</span></label>
                                <select id="id_programa" name="id_programa" required {{ !$puedeEditarAcademico ? 'disabled' : '' }}
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                                    <option value="" disabled>-- Seleccione un Programa --</option>
                                    @foreach($programas ?? [] as $prog)
                                        @php
                                            $progId = $safeGet($prog, 'id_programa', $safeGet($prog, 'id'));
                                            $progNombre = $safeGet($prog, 'nombre_programa', $safeGet($prog, 'nombre'));
                                        @endphp
                                        <option value="{{ $progId }}" {{ old('id_programa', $safeGet($estudiante, 'id_programa')) == $progId ? 'selected' : '' }}>
                                            {{ $progNombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(!$puedeEditarAcademico)
                                    <input type="hidden" name="id_programa" value="{{ old('id_programa', $safeGet($estudiante, 'id_programa')) }}">
                                @endif
                                @error('id_programa') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Director de Unidad (Dinámico) -->
                            <div class="space-y-2">
                                <label for="id_director_unidad_visual" class="block text-sm font-semibold text-gray-700">Director de Unidad Asignado</label>
                                <select id="id_director_unidad_visual" disabled 
                                        class="block w-full rounded-xl border-gray-300 bg-gray-100/80 shadow-sm text-gray-600 text-sm py-2.5 cursor-not-allowed font-medium">
                                    <option value="" disabled selected>-- Asignación Automática --</option>
                                    @foreach($directores ?? [] as $director)
                                        @php 
                                            $dirId = $safeGet($director, 'id_director_unidad', $safeGet($director, 'id', $safeGet($director, 'id_usuario'))); 
                                            $dirNombre = $safeGet($director, 'nombre_director', $safeGet($director, 'nombre', $safeGet($director, 'nombre_completo'))); 
                                        @endphp
                                        <option value="{{ $dirId }}" {{ old('id_director_unidad', old('id_docente', $safeGet($estudiante, 'id_docente'))) == $dirId ? 'selected' : '' }}>
                                            {{ $dirNombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="id_director_unidad" name="id_director_unidad" value="{{ old('id_director_unidad', $safeGet($estudiante, 'id_docente')) }}">
                                <input type="hidden" id="id_docente" name="id_docente" value="{{ old('id_docente', $safeGet($estudiante, 'id_docente')) }}">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Semestre -->
                            <div class="space-y-2">
                                <label for="semestre" class="block text-sm font-semibold text-gray-700">Semestre</label>
                                <select id="semestre" name="semestre" {{ !$puedeEditarAcademico ? 'disabled' : '' }} 
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900 bg-white' }} text-sm py-2.5">
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
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                                    <option value="Diurna" {{ old('jornada', $safeGet($estudiante, 'jornada')) == 'Diurna' ? 'selected' : '' }}>Diurna</option>
                                    <option value="Nocturna" {{ old('jornada', $safeGet($estudiante, 'jornada')) == 'Nocturna' ? 'selected' : '' }}>Nocturna</option>
                                    <option value="Sabatina" {{ old('jornada', $safeGet($estudiante, 'jornada')) == 'Sabatina' ? 'selected' : '' }}>Sabatina</option>
                                </select>
                                @if(!$puedeEditarAcademico)
                                    <input type="hidden" name="jornada" value="{{ old('jornada', $safeGet($estudiante, 'jornada')) }}">
                                @endif
                                @error('jornada') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- ¿Trabaja? -->
                            <div class="space-y-2">
                                <label for="trabaja" class="block text-sm font-semibold text-gray-700">¿Trabaja Actualmente?</label>
                                <select id="trabaja" name="trabaja" {{ !$puedeEditarAcademico ? 'disabled' : '' }}
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                                    <option value="Si" {{ $trabajaActual == 'Si' ? 'selected' : '' }}>Sí</option>
                                    <option value="No" {{ $trabajaActual == 'No' ? 'selected' : '' }}>No</option>
                                </select>
                                @if(!$puedeEditarAcademico)
                                    <input type="hidden" name="trabaja" value="{{ $trabajaActual }}">
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- SECCIÓN 2: ANÁLISIS DE RIESGO Y ESTILOS DE VIDA          -->
                    <!-- ======================================================== -->
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
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$esAdmin ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900 bg-white' }} text-sm py-2.5">
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

                    <!-- ======================================================== -->
                    <!-- SECCIÓN 3: ORIENTACIÓN PSICOLÓGICA Y ACOMPAÑAMIENTO PIAE -->
                    <!-- ======================================================== -->
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
                                <select id="nivel_servicio" name="nivel_servicio" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 text-gray-900 bg-white text-sm py-2.5">
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

                        <!-- Observaciones / Diagnóstico confidencial -->
                        <div class="space-y-2">
                            <label for="observaciones" class="block text-sm font-semibold text-gray-700">
                                Observaciones, Diagnóstico y Ruta de Atención
                            </label>
                            <textarea id="observaciones" name="observaciones" rows="5" 
                                      {{ !$puedeEditarOrientacion ? 'readonly' : '' }}
                                      placeholder="El sistema asigna la ruta de atención automáticamente, pero el equipo de Bienestar puede ajustar o complementar las recomendaciones aquí..."
                                      class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 {{ !$puedeEditarOrientacion ? 'bg-slate-50 text-slate-600' : 'text-gray-900 bg-white' }} text-sm">{{ $observacionesActual }}</textarea>
                        </div>

                        @if(!$puedeEditarOrientacion)
                            <p class="text-xs text-slate-500 flex items-center gap-1.5 pt-1">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                La orientación es generada de forma automatizada por el PIAE y solo puede ser alterada por el profesional de Psicología o Administrador.
                            </p>
                        @endif
                    </div>

                    <!-- ======================================================== -->
                    <!-- BOTONES DE ACCIÓN                                       -->
                    <!-- ======================================================== -->
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

    <!-- ======================================================== -->
    <!-- JAVASCRIPT DE ASIGNACIÓN DINÁMICA                        -->
    <!-- ======================================================== -->
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
            
            const elementosBloqueados = formulario.querySelectorAll('select[disabled], input[disabled]');
            elementosBloqueados.forEach(function (elemento) {
                elemento.removeAttribute('disabled');
            });
        }
    </script>
</x-app-layout>