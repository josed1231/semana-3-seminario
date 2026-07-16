<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Estudiante: ') }} {{ $estudiante->nombre_estudiante }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('estudiantes.update', $estudiante->codigo_estudiante) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- SECCIÓN 1: DATOS ACADÉMICOS (Bloqueado para psicologo) -->
                    <div class="border-b border-gray-700 pb-4">
                        <h3 class="text-md font-bold text-blue-400 mb-4">Datos Académicos</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-400">Código del Estudiante (No modificable)</label>
                                <input type="text" value="{{ $estudiante->codigo_estudiante }}" disabled class="mt-1 block w-full rounded-md border-gray-700 bg-gray-750 text-gray-400 text-sm cursor-not-allowed">
                            </div>

                            <div>
                                <label for="correo" class="block text-sm font-medium text-gray-300">Correo Institucional <span class="text-red-500">*</span></label>
                                <input type="email" name="correo" id="correo" value="{{ old('correo', $estudiante->correo) }}" required 
                                       {{ auth()->user()->rol === 'psicologo' ? 'disabled' : '' }}
                                       class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 {{ auth()->user()->rol === 'psicologo' ? 'text-gray-450 bg-gray-950 cursor-not-allowed' : 'text-gray-100' }} text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('correo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="nombre_estudiante" class="block text-sm font-medium text-gray-300">Nombre Completo <span class="text-red-500">*</span></label>
                            <input type="text" name="nombre_estudiante" id="nombre_estudiante" value="{{ old('nombre_estudiante', $estudiante->nombre_estudiante) }}" required 
                                   {{ auth()->user()->rol === 'psicologo' ? 'disabled' : '' }}
                                   class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 {{ auth()->user()->rol === 'psicologo' ? 'text-gray-450 bg-gray-950 cursor-not-allowed' : 'text-gray-100' }} text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('nombre_estudiante') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div>
                                <label for="id_programa" class="block text-sm font-medium text-gray-300">Programa Académico <span class="text-red-500">*</span></label>
                                <select name="id_programa" id="id_programa" required 
                                        {{ auth()->user()->rol === 'psicologo' ? 'disabled' : '' }}
                                        class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 {{ auth()->user()->rol === 'psicologo' ? 'text-gray-450 bg-gray-950 cursor-not-allowed' : 'text-gray-100' }} text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach($programas as $programa)
                                        @php 
                                            $progId = $programa->id_programa ?? $programa->id; 
                                            $progNombre = $programa->nombre_programa ?? $programa->nombre;
                                        @endphp
                                        <option value="{{ $progId }}" {{ old('id_programa', $estudiante->id_programa) == $progId ? 'selected' : '' }}>
                                            {{ $progNombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_programa') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="id_docente" class="block text-sm font-medium text-gray-300">Docente Tutor <span class="text-red-500">*</span></label>
                                <select name="id_docente" id="id_docente" required 
                                        {{ auth()->user()->rol === 'psicologo' ? 'disabled' : '' }}
                                        class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 {{ auth()->user()->rol === 'psicologo' ? 'text-gray-450 bg-gray-950 cursor-not-allowed' : 'text-gray-100' }} text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach($docentes as $docente)
                                        @php 
                                            $docId = $docente->id_docente ?? $docente->id; 
                                            $docNombre = $docente->nombre_docente ?? $docente->nombre;
                                        @endphp
                                        <option value="{{ $docId }}" {{ old('id_docente', $estudiante->id_docente) == $docId ? 'selected' : '' }}>
                                            {{ $docNombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_docente') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="w-full md:w-1/2 mt-4">
                            <label for="promedio" class="block text-sm font-medium text-gray-300">Promedio <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0" max="5.0" name="promedio" id="promedio" value="{{ old('promedio', $estudiante->promedio) }}" required 
                                   {{ auth()->user()->rol === 'psicologo' ? 'disabled' : '' }}
                                   class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 {{ auth()->user()->rol === 'psicologo' ? 'text-gray-450 bg-gray-950 cursor-not-allowed' : 'text-gray-100' }} text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('promedio') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- SECCIÓN 2: ESTILO DE VIDA Y RIESGO (Bloqueado para psicologo) -->
                    <div class="border-b border-gray-700 pb-4">
                        <h3 class="text-md font-bold text-yellow-500 mb-4">Análisis de Riesgo y Estilos de Vida</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nivel_riesgo" class="block text-sm font-medium text-gray-300">Nivel de Riesgo</label>
                                <select name="nivel_riesgo" id="nivel_riesgo" 
                                        {{ auth()->user()->rol === 'psicologo' ? 'disabled' : '' }}
                                        class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 {{ auth()->user()->rol === 'psicologo' ? 'text-gray-450 bg-gray-950 cursor-not-allowed' : 'text-gray-100' }} text-sm">
                                    <option value="Bajo" {{ old('nivel_riesgo', optional($estudiante->riesgo)->nivel_riesgo) == 'Bajo' ? 'selected' : '' }}>Bajo</option>
                                    <option value="Medio" {{ old('nivel_riesgo', optional($estudiante->riesgo)->nivel_riesgo) == 'Medio' ? 'selected' : '' }}>Medio</option>
                                    <option value="Alto" {{ old('nivel_riesgo', optional($estudiante->riesgo)->nivel_riesgo) == 'Alto' ? 'selected' : '' }}>Alto</option>
                                </select>
                            </div>

                            <div>
                                <label for="horas_estudio_semanal" class="block text-sm font-medium text-gray-300">Horas de Estudio Semanal</label>
                                <input type="number" name="horas_estudio_semanal" id="horas_estudio_semanal" value="{{ old('horas_estudio_semanal', optional($estudiante->estiloVida)->horas_estudio_semanal) }}"
                                       {{ auth()->user()->rol === 'psicologo' ? 'disabled' : '' }}
                                       class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 {{ auth()->user()->rol === 'psicologo' ? 'text-gray-450 bg-gray-950 cursor-not-allowed' : 'text-gray-100' }} text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div>
                                <label for="trabaja" class="block text-sm font-medium text-gray-300">¿Trabaja actualmente?</label>
                                <select name="trabaja" id="trabaja" 
                                        {{ auth()->user()->rol === 'psicologo' ? 'disabled' : '' }}
                                        class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 {{ auth()->user()->rol === 'psicologo' ? 'text-gray-450 bg-gray-950 cursor-not-allowed' : 'text-gray-100' }} text-sm">
                                    <option value="no" {{ old('trabaja', optional($estudiante->estiloVida)->trabaja) == 'no' ? 'selected' : '' }}>No</option>
                                    <option value="si" {{ old('trabaja', optional($estudiante->estiloVida)->trabaja) == 'si' ? 'selected' : '' }}>Sí</option>
                                </select>
                            </div>

                            <div>
                                <label for="detalles" class="block text-sm font-medium text-gray-300">Detalles adicionales del riesgo</label>
                                <textarea name="detalles" id="detalles" rows="2" 
                                          {{ auth()->user()->rol === 'psicologo' ? 'disabled' : '' }}
                                          class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 {{ auth()->user()->rol === 'psicologo' ? 'text-gray-450 bg-gray-950 cursor-not-allowed' : 'text-gray-100' }} text-sm">{{ old('detalles', optional($estudiante->riesgo)->detalles) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: ORIENTACIÓN PSICOPEDAGÓGICA (Bloqueado para dir_bienestar y dir_unidad) -->
                    <div class="pb-4">
                        <h3 class="text-md font-bold text-green-500 mb-4">Seguimiento Psicoorientación</h3>
                        <div>
                            <label for="observaciones" class="block text-sm font-medium text-gray-300">Observaciones del Psicólogo</label>
                            <textarea name="observaciones" id="observaciones" rows="4" 
                                      {{ in_array(auth()->user()->rol, ['dir_bienestar', 'dir_unidad']) ? 'disabled' : '' }}
                                      placeholder="Espacio reservado para las notas confidenciales de psicoorientación..."
                                      class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 {{ in_array(auth()->user()->rol, ['dir_bienestar', 'dir_unidad']) ? 'text-gray-450 bg-gray-950 cursor-not-allowed' : 'text-gray-100' }} text-sm">{{ old('observaciones', optional($estudiante->orientacionPsicologica)->observaciones) }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-700">
                        <a href="{{ route('dashboard') }}" class="bg-gray-700 hover:bg-gray-600 text-gray-200 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            Guardar Cambios
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>