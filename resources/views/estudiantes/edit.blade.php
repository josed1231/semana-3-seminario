<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight">
            {{ __('Editar Estudiante: ') }} {{ $estudiante->nombre_estudiante }}
        </h2>
    </x-slot>

    <div class="py-10 bg-gradient-to-tr from-stone-50 via-green-50/30 to-orange-50/20 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl rounded-3xl p-6 md:p-10 border border-emerald-100">
                
                <form id="editEstudianteForm" action="{{ route('estudiantes.update', $estudiante->codigo_estudiante) }}" method="POST" class="space-y-8" onsubmit="prepararEnvio()">
                    @csrf
                    @method('PUT')

                    @php
                        $esPsicologo = auth()->user()->rol === 'psicologo';
                        $noEsAdmin = auth()->user()->rol !== 'admin';
                        $esDirectivo = in_array(auth()->user()->rol, ['dir_bienestar', 'dir_unidad']);
                    @endphp

                    <div class="bg-emerald-50/40 p-6 md:p-8 rounded-2xl border border-emerald-100/80 space-y-6">
                        <h3 class="text-lg font-bold text-emerald-950 border-b border-emerald-100 pb-3">Datos Académicos</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Código del Estudiante (No modificable)</label>
                                <input type="text" value="{{ $estudiante->codigo_estudiante }}" disabled class="block w-full rounded-xl border-gray-300 bg-gray-50 text-gray-500 text-sm cursor-not-allowed shadow-sm py-2.5">
                            </div>

                            <div class="space-y-2">
                                <label for="correo" class="block text-sm font-semibold text-gray-700">Correo Institucional <span class="text-red-500">*</span></label>
                                <input type="email" id="correo" value="{{ old('correo', $estudiante->correo) }}" required 
                                       {{ $esPsicologo ? 'disabled' : 'name=correo' }}
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ $esPsicologo ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900' }} text-sm py-2.5">
                                @if($esPsicologo)
                                    <input type="hidden" name="correo" value="{{ old('correo', $estudiante->correo) }}">
                                @endif
                                @error('correo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="nombre_estudiante" class="block text-sm font-semibold text-gray-700">Nombre Completo <span class="text-red-500">*</span></label>
                            <input type="text" id="nombre_estudiante" value="{{ old('nombre_estudiante', $estudiante->nombre_estudiante) }}" required 
                                   {{ $esPsicologo ? 'disabled' : 'name=nombre_estudiante' }}
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ $esPsicologo ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900' }} text-sm py-2.5">
                            @if($esPsicologo)
                                <input type="hidden" name="nombre_estudiante" value="{{ old('nombre_estudiante', $estudiante->nombre_estudiante) }}">
                            @endif
                            @error('nombre_estudiante') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="id_programa" class="block text-sm font-semibold text-gray-700">Programa Académico <span class="text-red-500">*</span></label>
                                <select id="id_programa" name="id_programa" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 text-gray-900 text-sm py-2.5">
                                    <option value="" disabled>-- Seleccione un Programa --</option>
                                    @foreach($programas as $prog)
                                        <option value="{{ $prog->id_programa }}" {{ old('id_programa', $estudiante->id_programa) == $prog->id_programa ? 'selected' : '' }}>
                                            {{ $prog->nombre_programa ?? $prog->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_programa') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="id_director_unidad_visual" class="block text-sm font-semibold text-gray-700">Director de Unidad <span class="text-red-500">*</span></label>
                                <select id="id_director_unidad_visual" disabled 
                                        class="block w-full rounded-xl border-gray-300 bg-gray-50 shadow-sm text-gray-500 text-sm py-2.5 cursor-not-allowed">
                                    <option value="" disabled selected>-- Seleccione un Director --</option>
                                    @foreach($directores as $director)
                                        @php 
                                            $dirId = $director->id_director_unidad ?? $director->id ?? $director->id_usuario; 
                                            $dirNombre = $director->nombre_director ?? $director->nombre ?? $director->nombre_completo; 
                                        @endphp
                                        <option value="{{ $dirId }}" {{ old('id_director_unidad', $estudiante->id_director_unidad) == $dirId ? 'selected' : '' }}>
                                            {{ $dirNombre }}
                                        </option>
                                    @endforeach
                                </select>
                                
                                <input type="hidden" id="id_director_unidad" name="id_director_unidad" value="{{ old('id_director_unidad', $estudiante->id_director_unidad) }}">
                                
                                @error('id_director_unidad') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label for="promedio" class="block text-sm font-semibold text-gray-700">Promedio <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" min="0" max="5.0" id="promedio" value="{{ old('promedio', $estudiante->promedio) }}" required 
                                       {{ $esPsicologo ? 'disabled' : 'name=promedio' }}
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ $esPsicologo ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900' }} text-sm py-2.5">
                                @if($esPsicologo)
                                    <input type="hidden" name="promedio" value="{{ old('promedio', $estudiante->promedio) }}">
                                @endif
                                @error('promedio') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="semestre" class="block text-sm font-semibold text-gray-700">Semestre <span class="text-emerald-700 text-xs">(Del Cuestionario)</span></label>
                                @php
                                    $semestreActual = old('semestre', optional($estudiante->cuestionario)->semestre ?? $estudiante->semestre);
                                @endphp
                                <select id="semestre" required 
                                        {{ $esPsicologo ? 'disabled' : 'name=semestre' }}
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ $esPsicologo ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900' }} text-sm py-2.5">
                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ $semestreActual == $i ? 'selected' : '' }}>Semestre {{ $i }}</option>
                                    @endfor
                                </select>
                                @if($esPsicologo)
                                    <input type="hidden" name="semestre" value="{{ $semestreActual }}">
                                @endif
                                @error('semestre') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="jornada" class="block text-sm font-semibold text-gray-700">Jornada <span class="text-red-500">*</span></label>
                                <select id="jornada" required 
                                        {{ $esPsicologo ? 'disabled' : 'name=jornada' }}
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ $esPsicologo ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900' }} text-sm py-2.5">
                                    <option value="Diurna" {{ old('jornada', $estudiante->jornada) == 'Diurna' ? 'selected' : '' }}>Diurna</option>
                                    <option value="Nocturna" {{ old('jornada', $estudiante->jornada) == 'Nocturna' ? 'selected' : '' }}>Nocturna</option>
                                    <option value="Sabatina" {{ old('jornada', $estudiante->jornada) == 'Sabatina' ? 'selected' : '' }}>Sabatina</option>
                                </select>
                                @if($esPsicologo)
                                    <input type="hidden" name="jornada" value="{{ old('jornada', $estudiante->jornada) }}">
                                @endif
                                @error('jornada') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="trabaja" class="block text-sm font-semibold text-gray-700">¿Actualmente trabaja? <span class="text-red-500">*</span></label>
                            @php
                                $trabajaActual = old('trabaja', optional($estudiante->cuestionario)->trabaja ?? $estudiante->trabaja);
                            @endphp
                            <select id="trabaja" required 
                                    {{ $noEsAdmin ? 'disabled' : 'name=trabaja' }}
                                    class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ $noEsAdmin ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900' }} text-sm py-2.5">
                                <option value="" disabled>-- Seleccione --</option>
                                <option value="Si" {{ $trabajaActual == 'Si' ? 'selected' : '' }}>Sí</option>
                                <option value="No" {{ $trabajaActual == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                            @if($noEsAdmin)
                                <input type="hidden" name="trabaja" value="{{ $trabajaActual }}">
                            @endif
                            @error('trabaja') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="p-6 md:p-8 bg-white rounded-2xl border border-gray-200 space-y-6">
                        <h3 class="text-lg font-bold text-orange-600 border-b pb-3">⚠️ Análisis de Riesgo y Estilos de Vida</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="nivel_riesgo" class="block text-sm font-semibold text-gray-700">Nivel de Riesgo</label>
                                <select id="nivel_riesgo" 
                                        {{ $esPsicologo ? 'disabled' : 'name=nivel_riesgo' }}
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ $esPsicologo ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900' }} text-sm py-2.5">
                                    <option value="Bajo" {{ old('nivel_riesgo', optional($estudiante->riesgo)->nivel_riesgo) == 'Bajo' ? 'selected' : '' }}>Bajo</option>
                                    <option value="Medio" {{ old('nivel_riesgo', optional($estudiante->riesgo)->nivel_riesgo) == 'Medio' ? 'selected' : '' }}>Medio</option>
                                    <option value="Alto" {{ old('nivel_riesgo', optional($estudiante->riesgo)->nivel_riesgo) == 'Alto' ? 'selected' : '' }}>Alto</option>
                                </select>
                                @if($esPsicologo)
                                    <input type="hidden" name="nivel_riesgo" value="{{ old('nivel_riesgo', optional($estudiante->riesgo)->nivel_riesgo) }}">
                                @endif
                            </div>

                            <div class="space-y-2">
                                <label for="actividad" class="block text-sm font-semibold text-gray-700">Actividades Frecuentes (Estilo de Vida)</label>
                                <input type="text" id="actividad" 
                                       value="{{ old('actividad', optional($estudiante->cuestionario)->actividad) }}"
                                       placeholder="Actividades que realiza el estudiante..."
                                       {{ $noEsAdmin ? 'disabled' : 'name=actividad' }}
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ $noEsAdmin ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900' }} text-sm py-2.5">
                                @if($noEsAdmin)
                                    <input type="hidden" name="actividad" value="{{ old('actividad', optional($estudiante->cuestionario)->actividad) }}">
                                @endif
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="detalles" class="block text-sm font-semibold text-gray-700">Detalles adicionales del riesgo</label>
                            <textarea id="detalles" rows="2" 
                                      {{ $esPsicologo ? 'disabled' : 'name=detalles' }}
                                      class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ $esPsicologo ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900' }} text-sm">{{ old('detalles', optional($estudiante->riesgo)->detalles) }}</textarea>
                            @if($esPsicologo)
                                <textarea name="detalles" class="hidden">{{ old('detalles', optional($estudiante->riesgo)->detalles) }}</textarea>
                            @endif
                        </div>
                    </div>

                    <div class="p-6 md:p-8 bg-white rounded-2xl border border-gray-200 space-y-6">
                        <h3 class="text-lg font-bold text-emerald-700 border-b pb-3">💼 Seguimiento Psicoorientación</h3>
                        <div class="space-y-2">
                            <label for="observaciones" class="block text-sm font-semibold text-gray-700">Observaciones del Psicólogo</label>
                            <textarea id="observaciones" rows="4" 
                                      {{ $esDirectivo ? 'disabled' : 'name=observaciones' }}
                                      placeholder="Espacio reservado para las notas confidenciales de psicoorientación..."
                                      class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 {{ $esDirectivo ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'text-gray-900' }} text-sm">{{ old('observaciones', optional($estudiante->orientacionPsicologica)->observaciones) }}</textarea>
                            @if($esDirectivo)
                                <textarea name="observaciones" class="hidden">{{ old('observaciones', optional($estudiante->orientacionPsicologica)->observaciones) }}</textarea>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('dashboard') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-sm border border-gray-200">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-[#f17a28] hover:bg-[#d66213] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-md">
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

            function actualizarDirector() {
                if (!programmeSelect || !directorSelectVisual || !directorInputHidden) return;

                const programaTexto = programmeSelect.options[programmeSelect.selectedIndex].text.toLowerCase();
                let directorObjetivo = "";

                if (programaTexto.includes('sistemas')) {
                    directorObjetivo = "director ingeniería";
                } else if (programaTexto.includes('contaduría') || programaTexto.includes('contaduria')) {
                    directorObjetivo = "director contaduría";
                } else if (programaTexto.includes('agropecuaria')) {
                    directorObjetivo = "director agropecuaria";
                }

                if (directorObjetivo !== "") {
                    for (let i = 0; i < directorSelectVisual.options.length; i++) {
                        const opcionTexto = directorSelectVisual.options[i].text.toLowerCase();
                        if (opcionTexto.includes(directorObjetivo)) {
                            // Actualizamos la selección visual
                            directorSelectVisual.selectedIndex = i;
                            // Sincronizamos el valor numérico real en el input hidden para Laravel
                            directorInputHidden.value = directorSelectVisual.options[i].value;
                            return;
                        }
                    }
                }
            }

            if (programmeSelect) {
                programmeSelect.addEventListener('change', actualizarDirector);
                
                // Ejecutar al inicio de la carga para asegurar sincronización
                actualizarDirector();
            }
        });

        function prepararEnvio() {
            const formulario = document.getElementById('editEstudianteForm');
            // Removemos disabled temporalmente antes del submit tradicional
            const elementosBloqueados = formulario.querySelectorAll('select[disabled], input[disabled]');
            elementosBloqueados.forEach(function (elemento) {
                elemento.removeAttribute('disabled');
            });
        }
    </script>
</x-app-layout>