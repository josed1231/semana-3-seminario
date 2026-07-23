<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Editar Estudiante: ') }} <span class="text-emerald-700">{{ $estudiante->nombre_estudiante }}</span>
            </h2>
            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                Código: {{ $estudiante->codigo_estudiante }}
            </span>
        </div>
    </x-slot>

    <div class="py-10 bg-gradient-to-tr from-stone-50 via-green-50/30 to-orange-50/20 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl rounded-3xl p-6 md:p-10 border border-emerald-100/60">

                @php
                    $userRol = auth()->user()->rol ?? '';
                    $esAdmin = $userRol === 'admin';
                    $esPsicologo = in_array($userRol, ['psicologo', 'bienestar']);
                    $esDirectivo = in_array($userRol, ['dir_bienestar', 'dir_unidad']);

                    // Permisos de edición por sección
                    $puedeEditarAcademico = $esAdmin || $esDirectivo;
                    $puedeEditarOrientacion = $esAdmin || $esPsicologo;

                    // Fallback robusto para Correo Institucional
                    $correoActual = old('correo', 
                        $estudiante->correo 
                        ?? optional($estudiante->usuario)->email 
                        ?? optional($estudiante->usuario)->correo 
                        ?? optional($estudiante->user)->email 
                        ?? optional($estudiante->cuestionario)->correo 
                        ?? ''
                    );

                    // Fallback para Cédula / Documento de Identidad
                    $cedulaActual = old('cedula', 
                        $estudiante->cedula 
                        ?? $estudiante->numero_documento 
                        ?? $estudiante->documento 
                        ?? optional($estudiante->cuestionario)->cedula 
                        ?? ''
                    );

                    // Valor dinámico de la actividad de estilo de vida
                    $actividadValor = old('actividad', 
                        optional($estudiante->cuestionario)->actividad 
                        ?? $estudiante->actividades_estilo_vida 
                        ?? optional($estudiante->estiloVida)->actividad
                    );

                    // Nivel de Riesgo actual
                    $riesgoActual = old('nivel_riesgo', optional($estudiante->riesgo)->nivel_riesgo ?? 'Bajo');

                    // Datos de Orientación Psicológica
                    $nivelServicioActual = old('nivel_servicio', optional($estudiante->orientacionPsicologica)->nivel_servicio ?? 'Tutoría Académica Standard');
                    $observacionesActual = old('observaciones', optional($estudiante->orientacionPsicologica)->observaciones);
                @endphp

                <form id="editEstudianteForm" action="{{ route('estudiantes.update', $estudiante->codigo_estudiante) }}" method="POST" class="space-y-8" onsubmit="prepararEnvio()">
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
                            <!-- Cédula / Documento -->
                            <div class="space-y-2">
                                <label for="cedula" class="block text-sm font-semibold text-gray-700">Cédula / Documento <span class="text-red-500">*</span></label>
                                <input type="text" id="cedula" value="{{ $cedulaActual }}" required 
                                       {{ !$puedeEditarAcademico ? 'readonly' : 'name=cedula' }}
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                                @if(!$puedeEditarAcademico)
                                    <input type="hidden" name="cedula" value="{{ $cedulaActual }}">
                                @endif
                                @error('cedula') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Código del Estudiante -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Código del Estudiante</label>
                                <input type="text" value="{{ $estudiante->codigo_estudiante }}" disabled class="block w-full rounded-xl border-gray-300 bg-gray-100/70 text-gray-500 text-sm cursor-not-allowed shadow-sm py-2.5">
                            </div>

                            <!-- Correo Institucional -->
                            <div class="space-y-2">
                                <label for="correo" class="block text-sm font-semibold text-gray-700">Correo Institucional <span class="text-red-500">*</span></label>
                                <input type="email" id="correo" value="{{ $correoActual }}" required 
                                       {{ !$puedeEditarAcademico ? 'readonly' : 'name=correo' }}
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                                @if(!$puedeEditarAcademico)
                                    <input type="hidden" name="correo" value="{{ $correoActual }}">
                                @endif
                                @error('correo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Nombre Completo -->
                        <div class="space-y-2">
                            <label for="nombre_estudiante" class="block text-sm font-semibold text-gray-700">Nombre Completo <span class="text-red-500">*</span></label>
                            <input type="text" id="nombre_estudiante" value="{{ old('nombre_estudiante', $estudiante->nombre_estudiante) }}" required 
                                   {{ !$puedeEditarAcademico ? 'readonly' : 'name=nombre_estudiante' }}
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                            @if(!$puedeEditarAcademico)
                                <input type="hidden" name="nombre_estudiante" value="{{ old('nombre_estudiante', $estudiante->nombre_estudiante) }}">
                            @endif
                            @error('nombre_estudiante') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Programa Académico -->
                            <div class="space-y-2">
                                <label for="id_programa" class="block text-sm font-semibold text-gray-700">Programa Académico <span class="text-red-500">*</span></label>
                                <select id="id_programa" {{ !$puedeEditarAcademico ? 'disabled' : 'name=id_programa' }} required 
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                                    <option value="" disabled>-- Seleccione un Programa --</option>
                                    @foreach($programas as $prog)
                                        <option value="{{ $prog->id_programa }}" {{ old('id_programa', $estudiante->id_programa) == $prog->id_programa ? 'selected' : '' }}>
                                            {{ $prog->nombre_programa ?? $prog->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(!$puedeEditarAcademico)
                                    <input type="hidden" name="id_programa" value="{{ old('id_programa', $estudiante->id_programa) }}">
                                @endif
                                @error('id_programa') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Director de Unidad (Dinámico) -->
                            <div class="space-y-2">
                                <label for="id_director_unidad_visual" class="block text-sm font-semibold text-gray-700">Director de Unidad Asignado</label>
                                <select id="id_director_unidad_visual" disabled 
                                        class="block w-full rounded-xl border-gray-300 bg-gray-100/80 shadow-sm text-gray-600 text-sm py-2.5 cursor-not-allowed font-medium">
                                    <option value="" disabled selected>-- Asignación Automática --</option>
                                    @foreach($directores as $director)
                                        @php 
                                            $dirId = $director->id_director_unidad ?? $director->id ?? $director->id_usuario; 
                                            $dirNombre = $director->nombre_director ?? $director->nombre ?? $director->nombre_completo; 
                                        @endphp
                                        <option value="{{ $dirId }}" {{ old('id_director_unidad', old('id_docente', $estudiante->id_docente)) == $dirId ? 'selected' : '' }}>
                                            {{ $dirNombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="id_director_unidad" name="id_director_unidad" value="{{ old('id_director_unidad', $estudiante->id_docente) }}">
                                <input type="hidden" id="id_docente" name="id_docente" value="{{ old('id_docente', $estudiante->id_docente) }}">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Semestre -->
                            <div class="space-y-2">
                                <label for="semestre" class="block text-sm font-semibold text-gray-700">Semestre</label>
                                @php
                                    $semestreActual = old('semestre', optional($estudiante->cuestionario)->semestre ?? $estudiante->semestre ?? 1);
                                @endphp
                                <select id="semestre" {{ !$puedeEditarAcademico ? 'disabled' : 'name=semestre' }} 
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
                                <select id="jornada" required {{ !$puedeEditarAcademico ? 'disabled' : 'name=jornada' }}
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                                    <option value="Diurna" {{ old('jornada', $estudiante->jornada) == 'Diurna' ? 'selected' : '' }}>Diurna</option>
                                    <option value="Nocturna" {{ old('jornada', $estudiante->jornada) == 'Nocturna' ? 'selected' : '' }}>Nocturna</option>
                                    <option value="Sabatina" {{ old('jornada', $estudiante->jornada) == 'Sabatina' ? 'selected' : '' }}>Sabatina</option>
                                </select>
                                @if(!$puedeEditarAcademico)
                                    <input type="hidden" name="jornada" value="{{ old('jornada', $estudiante->jornada) }}">
                                @endif
                                @error('jornada') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- ¿Trabaja? -->
                            <div class="space-y-2">
                                <label for="trabaja" class="block text-sm font-semibold text-gray-700">¿Trabaja Actualmente?</label>
                                @php
                                    $trabajaActual = old('trabaja', optional($estudiante->cuestionario)->trabaja ?? $estudiante->trabaja ?? 'No');
                                @endphp
                                <select id="trabaja" {{ !$puedeEditarAcademico ? 'disabled' : 'name=trabaja' }}
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
                                <select id="nivel_riesgo" {{ !$esAdmin ? 'disabled' : 'name=nivel_riesgo' }}
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
                                <input type="text" id="actividad" value="{{ $actividadValor }}"
                                       placeholder="Actividades extracurriculares o hábitos..."
                                       {{ !$puedeEditarAcademico ? 'readonly' : 'name=actividad' }}
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$puedeEditarAcademico ? 'bg-gray-50 text-gray-500' : 'text-gray-900 bg-white' }} text-sm py-2.5">
                                @if(!$puedeEditarAcademico)
                                    <input type="hidden" name="actividad" value="{{ $actividadValor }}">
                                @endif
                            </div>
                        </div>

                        <!-- Detalles Adicionales -->
                        <div class="space-y-2">
                            <label for="detalles" class="block text-sm font-semibold text-gray-700">Detalles Adicionales del Riesgo</label>
                            <textarea id="detalles" rows="2" 
                                      {{ !$esAdmin ? 'readonly' : 'name=detalles' }}
                                      placeholder="Anotaciones técnicas del nivel de deserción..."
                                      class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ !$esAdmin ? 'bg-gray-50 text-gray-500' : 'text-gray-900 bg-white' }} text-sm">{{ old('detalles', optional($estudiante->riesgo)->detalles) }}</textarea>
                            @if(!$esAdmin)
                                <input type="hidden" name="detalles" value="{{ old('detalles', optional($estudiante->riesgo)->detalles) }}">
                            @endif
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
                            <textarea id="observaciones" rows="5" 
                                      {{ !$puedeEditarOrientacion ? 'readonly' : 'name=observaciones' }}
                                      placeholder="El sistema asigna la ruta de atención automáticamente, pero el equipo de Bienestar puede ajustar o complementar las recomendaciones aquí..."
                                      class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 {{ !$puedeEditarOrientacion ? 'bg-slate-50 text-slate-600' : 'text-gray-900 bg-white' }} text-sm">{{ $observacionesActual }}</textarea>
                            @if(!$puedeEditarOrientacion)
                                <input type="hidden" name="observaciones" value="{{ $observacionesActual }}">
                            @endif
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
                        <a href="{{ route('alertas.monitoreo') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-bold transition shadow-sm border border-gray-200">
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

    <!-- JAVASCRIPT DE ASIGNACIÓN DINÁMICA -->
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