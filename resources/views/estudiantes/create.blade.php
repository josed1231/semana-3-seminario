<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Nuevo Estudiante') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('estudiantes.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="codigo_estudiante" class="block text-sm font-medium text-gray-300">Código del Estudiante <span class="text-red-500">*</span></label>
                            <input type="text" name="codigo_estudiante" id="codigo_estudiante" value="{{ old('codigo_estudiante') }}" required placeholder="Ej: EST-2026-002" class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('codigo_estudiante') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="correo" class="block text-sm font-medium text-gray-300">Correo Institucional <span class="text-red-500">*</span></label>
                            <input type="email" name="correo" id="correo" value="{{ old('correo') }}" required placeholder="ejemplo@cotecnova.edu.co" class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('correo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="nombre_estudiante" class="block text-sm font-medium text-gray-300">Nombre Completo <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre_estudiante" id="nombre_estudiante" value="{{ old('nombre_estudiante') }}" required placeholder="Nombre del estudiante" class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('nombre_estudiante') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="id_programa" class="block text-sm font-medium text-gray-300">Programa Académico <span class="text-red-500">*</span></label>
                            <select name="id_programa" id="id_programa" required class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Seleccione un programa...</option>
                                @foreach($programas as $programa)
                                    @php 
                                        $progId = $programa->id_programa ?? $programa->id; 
                                        $progNombre = $programa->nombre_programa ?? $programa->nombre;
                                    @endphp
                                    <option value="{{ $progId }}" {{ old('id_programa') == $progId ? 'selected' : '' }}>
                                        {{ $progNombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_programa') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="id_director_unidad" class="block text-sm font-medium text-gray-400">Director de Unidad (Asignado Automáticamente)</label>
                            <select name="id_director_unidad" id="id_director_unidad" required class="mt-1 block w-full rounded-md border-gray-700 bg-gray-950 text-gray-400 text-sm cursor-not-allowed pointer-events-none">
                                <option value="" disabled selected>-- Primero elija un programa --</option>
                                @foreach($directores as $director)
                                    @php 
                                        $dirId = $director->id_director_unidad ?? $director->id; 
                                        $dirNombre = $director->nombre_director ?? $director->nombre; 
                                    @endphp
                                    <option value="{{ $dirId }}" {{ old('id_director_unidad') == $dirId ? 'selected' : '' }}>
                                        {{ $dirNombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_director_unidad') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="promedio" class="block text-sm font-medium text-gray-300">Promedio <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0" max="5.0" name="promedio" id="promedio" value="{{ old('promedio') }}" required placeholder="0.00 a 5.00" class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('promedio') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="semestre" class="block text-sm font-medium text-gray-300">Semestre <span class="text-red-500">*</span></label>
                            <select name="semestre" id="semestre" required class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Seleccione...</option>
                                @for ($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('semestre') == $i ? 'selected' : '' }}>Semestre {{ $i }}</option>
                                @endfor
                            </select>
                            @error('semestre') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="jornada" class="block text-sm font-medium text-gray-300">Jornada <span class="text-red-500">*</span></label>
                            <select name="jornada" id="jornada" required class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Seleccione...</option>
                                <option value="Diurna" {{ old('jornada') == 'Diurna' ? 'selected' : '' }}>Diurna</option>
                                <option value="Nocturna" {{ old('jornada') == 'Nocturna' ? 'selected' : '' }}>Nocturna</option>
                                <option value="Sabatina" {{ old('jornada') == 'Sabatina' ? 'selected' : '' }}>Sabatina</option>
                            </select>
                            @error('jornada') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-700">
                        <a href="{{ route('dashboard') }}" class="bg-gray-700 hover:bg-gray-600 text-gray-200 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            Registrar Estudiante
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Script de Automatización del Director según el Programa -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const programaSelect = document.getElementById('id_programa');
            const directorSelect = document.getElementById('id_director_unidad');

            // MAPEO: ID de Programa Académico => ID de su Director de Unidad correspondiente
            const programaAlDirector = {
                '1': '1', // Ejemplo: 1 (Ingeniería de Sistemas) -> 1 (Director Ingeniería)
                '2': '2', // Ejemplo: 2 (Tec. Agropecuaria)   -> 2 (Director Agro)
                '3': '3'  // Ejemplo: 3 (Contaduría Pública) -> 3 (Director Contaduría)
            };

            function actualizarDirector() {
                const programaId = programaSelect.value;
                const directorId = programaAlDirector[programaId];

                if (directorId) {
                    directorSelect.value = directorId;
                } else {
                    directorSelect.value = "";
                }
            }

            if (programaSelect && directorSelect) {
                programaSelect.addEventListener('change', actualizarDirector);
                actualizarDirector(); // Inicializar por si hay un valor "old"
            }
        });
    </script>
</x-app-layout>