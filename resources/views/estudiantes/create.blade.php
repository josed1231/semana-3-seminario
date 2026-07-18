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
                            <!-- Se añadió el evento onchange -->
                            <select name="id_programa" id="id_programa" required onchange="cargarDocentes(this.value)" class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                            <label for="id_docente" class="block text-sm font-medium text-gray-300">Docente Tutor <span class="text-red-500">*</span></label>
                            <select name="id_docente" id="id_docente" required class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Primero seleccione un programa...</option>
                            </select>
                            @error('id_docente') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="w-full md:w-1/2">
                        <label for="promedio" class="block text-sm font-medium text-gray-300">Promedio <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" min="0" max="5.0" name="promedio" id="promedio" value="{{ old('promedio') }}" required placeholder="0.00 a 5.00" class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('promedio') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
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

    <!-- Script para cargar docentes dinámicamente -->
    <script>
        function cargarDocentes(id_programa) {
            const selectDocente = document.getElementById('id_docente');
            
            if (!id_programa) {
                selectDocente.innerHTML = '<option value="">Primero seleccione un programa...</option>';
                return;
            }

            // Llamada a la ruta que creamos en web.php
            fetch(`/get-docentes/${id_programa}`)
                .then(response => response.json())
                .then(data => {
                    selectDocente.innerHTML = '<option value="">Seleccione un docente</option>';
                    data.forEach(docente => {
                        // Asegúrate de que las propiedades 'id' y 'nombre_docente' coincidan con tu tabla
                        // Ajusta 'nombre_docente' si en tu BD el campo se llama distinto (ej. 'nombre')
                        let docId = docente.id_docente ?? docente.id;
                        let docNombre = docente.nombre_docente ?? docente.nombre;
                        
                        selectDocente.innerHTML += `<option value="${docId}">${docNombre}</option>`;
                    });
                })
                .catch(error => console.error('Error al cargar docentes:', error));
        }
    </script>
</x-app-layout>