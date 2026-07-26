<x-app-layout>
    <x-slot name="header">
        <div class="rounded-2xl p-6 shadow-sm bg-[#004d2e]">
            <h2 class="font-bold text-2xl leading-tight m-0 text-white">
                {{ __('Registrar Nuevo Estudiante - Cotecnova') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 min-h-screen bg-[#f4f6f8]">
        <div class="max-w-4xl mx-auto px-3 sm:px-6">

            <!-- Banner de Alerta de Errores General -->
            @if(session('error'))
                <div class="mb-4 p-4 rounded-2xl bg-red-100 border border-red-300 text-red-800 text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 rounded-2xl bg-amber-100 border border-amber-300 text-amber-900 text-sm">
                    <p class="font-bold mb-1">Por favor corrige los siguientes errores:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-md sm:rounded-3xl p-8 border border-slate-200">
                
                <form action="{{ route('estudiantes.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Fila 1: Código y Cédula -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="codigo_estudiante" class="block text-xs font-bold text-slate-700 mb-1">
                                Código del Estudiante <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="codigo_estudiante" id="codigo_estudiante" value="{{ old('codigo_estudiante') }}" required class="rounded-xl px-3.5 py-2.5 text-sm w-full text-slate-800 bg-white border border-slate-300 focus:border-[#005a36] focus:ring-2 focus:ring-[#dcece4] outline-none transition-all">
                            @error('codigo_estudiante') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="cedula" class="block text-xs font-bold text-slate-700 mb-1">
                                Cédula / Documento de Identidad <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="cedula" id="cedula" value="{{ old('cedula') }}" required class="rounded-xl px-3.5 py-2.5 text-sm w-full text-slate-800 bg-white border border-slate-300 focus:border-[#005a36] focus:ring-2 focus:ring-[#dcece4] outline-none transition-all">
                            @error('cedula') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Fila 2: Nombre Completo y Correo Institucional -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nombre_estudiante" class="block text-xs font-bold text-slate-700 mb-1">
                                Nombre Completo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nombre_estudiante" id="nombre_estudiante" value="{{ old('nombre_estudiante') }}" required class="rounded-xl px-3.5 py-2.5 text-sm w-full text-slate-800 bg-white border border-slate-300 focus:border-[#005a36] focus:ring-2 focus:ring-[#dcece4] outline-none transition-all">
                            @error('nombre_estudiante') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="correo" class="block text-xs font-bold text-slate-700 mb-1">
                                Correo Institucional <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="correo" id="correo" value="{{ old('correo') }}" required class="rounded-xl px-3.5 py-2.5 text-sm w-full text-slate-800 bg-white border border-slate-300 focus:border-[#005a36] focus:ring-2 focus:ring-[#dcece4] outline-none transition-all">
                            @error('correo') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Fila 3: Programa Académico y Director de Unidad -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="id_programa" class="block text-xs font-bold text-slate-700 mb-1">
                                Programa Académico <span class="text-red-500">*</span>
                            </label>
                            <select name="id_programa" id="id_programa" required class="rounded-xl px-3.5 py-2.5 text-sm w-full text-slate-800 bg-white border border-slate-300 focus:border-[#005a36] focus:ring-2 focus:ring-[#dcece4] outline-none transition-all">
                                <option value="">Seleccione un programa...</option>
                                @foreach($programas as $programa)
                                    @php 
                                        $progId = $programa->id_programa ?? $programa->id; 
                                        $progNombre = $programa->nombre_programa ?? $programa->nombre;
                                        $docenteId = $programa->id_docente ?? $progId;
                                    @endphp
                                    <option value="{{ $progId }}" data-director="{{ $docenteId }}" {{ old('id_programa') == $progId ? 'selected' : '' }}>
                                        {{ $progNombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_programa') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="id_director_unidad_display" class="block text-xs font-bold text-slate-500 mb-1">
                                Director de Unidad (Asignado Automáticamente)
                            </label>
                            <!-- Input oculto para enviar el valor del director -->
                            <input type="hidden" name="id_director_unidad" id="id_director_unidad" value="{{ old('id_director_unidad') }}">
                            <select id="id_director_unidad_display" class="rounded-xl px-3.5 py-2.5 text-sm w-full text-slate-500 bg-slate-100 border border-slate-200 cursor-not-allowed pointer-events-none">
                                <option value="" disabled selected>-- Primero elija un programa --</option>
                                @foreach($directores as $director)
                                    @php 
                                        $dirId = $director->id_director_unidad ?? $director->id_docente ?? $director->id; 
                                        $dirNombre = $director->nombre_director ?? $director->nombre ?? 'Director '.$dirId; 
                                    @endphp
                                    <option value="{{ $dirId }}" {{ old('id_director_unidad') == $dirId ? 'selected' : '' }}>
                                        {{ $dirNombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_director_unidad') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Fila 4: Semestre, Jornada y Género -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="semestre" class="block text-xs font-bold text-slate-700 mb-1">
                                Semestre <span class="text-red-500">*</span>
                            </label>
                            <select name="semestre" id="semestre" required class="rounded-xl px-3.5 py-2.5 text-sm w-full text-slate-800 bg-white border border-slate-300 focus:border-[#005a36] focus:ring-2 focus:ring-[#dcece4] outline-none transition-all">
                                <option value="">Seleccione...</option>
                                @for ($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('semestre') == $i ? 'selected' : '' }}>Semestre {{ $i }}</option>
                                @endfor
                            </select>
                            @error('semestre') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="jornada" class="block text-xs font-bold text-slate-700 mb-1">
                                Jornada <span class="text-red-500">*</span>
                            </label>
                            <select name="jornada" id="jornada" required class="rounded-xl px-3.5 py-2.5 text-sm w-full text-slate-800 bg-white border border-slate-300 focus:border-[#005a36] focus:ring-2 focus:ring-[#dcece4] outline-none transition-all">
                                <option value="">Seleccione...</option>
                                <option value="Diurna" {{ old('jornada') == 'Diurna' ? 'selected' : '' }}>Diurna</option>
                                <option value="Nocturna" {{ old('jornada') == 'Nocturna' ? 'selected' : '' }}>Nocturna</option>
                                <option value="Sabatina" {{ old('jornada') == 'Sabatina' ? 'selected' : '' }}>Sabatina</option>
                            </select>
                            @error('jornada') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="genero" class="block text-xs font-bold text-slate-700 mb-1">
                                Género <span class="text-red-500">*</span>
                            </label>
                            <select name="genero" id="genero" required class="rounded-xl px-3.5 py-2.5 text-sm w-full text-slate-800 bg-white border border-slate-300 focus:border-[#005a36] focus:ring-2 focus:ring-[#dcece4] outline-none transition-all">
                                <option value="">Seleccione...</option>
                                @if(isset($generos) && count($generos) > 0)
                                    @foreach($generos as $g)
                                        @php $nombreGenero = $g->nombre ?? $g; @endphp
                                        <option value="{{ $nombreGenero }}" {{ old('genero') == $nombreGenero ? 'selected' : '' }}>
                                            {{ $nombreGenero }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="Masculino" {{ old('genero') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Femenino" {{ old('genero') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="No binario" {{ old('genero') == 'No binario' ? 'selected' : '' }}>No binario</option>
                                    <option value="Otro" {{ old('genero') == 'Otro' ? 'selected' : '' }}>Otro / Prefiero no decir</option>
                                @endif
                            </select>
                            @error('genero') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                        <a href="{{ route('alertas.monitoreo') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-slate-200 hover:bg-slate-300 text-slate-800 transition-colors shadow-sm decoration-none">
                            Cancelar
                        </a>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-[#f17a28] hover:bg-[#d66213] text-white transition-colors shadow-sm border-none cursor-pointer">
                            Registrar Estudiante
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Script Dinámico para Asignar Director -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const programaSelect = document.getElementById('id_programa');
            const directorSelectDisplay = document.getElementById('id_director_unidad_display');
            const directorHiddenInput = document.getElementById('id_director_unidad');

            function actualizarDirector() {
                const selectedOption = programaSelect.options[programaSelect.selectedIndex];
                const directorId = selectedOption ? selectedOption.getAttribute('data-director') : null;

                if (directorId) {
                    directorSelectDisplay.value = directorId;
                    directorHiddenInput.value = directorId;
                } else {
                    // Fallback al primer director de la lista si el programa no especifica uno
                    if (directorSelectDisplay.options.length > 1) {
                        directorSelectDisplay.selectedIndex = 1;
                        directorHiddenInput.value = directorSelectDisplay.options[1].value;
                    } else {
                        directorSelectDisplay.value = "";
                        directorHiddenInput.value = "";
                    }
                }
            }

            if (programaSelect) {
                programaSelect.addEventListener('change', actualizarDirector);
                actualizarDirector();
            }
        });
    </script>
</x-app-layout>