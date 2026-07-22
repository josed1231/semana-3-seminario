<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Programas Académicos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Mensajes de éxito -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- Formulario para Crear Programa -->
                <div class="mb-8 p-4 bg-gray-50 border rounded-lg">
                    <h3 class="text-lg font-bold mb-4">Agregar Nuevo Programa Académico</h3>
                    <form action="{{ route('programas.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre del Programa</label>
                            <input type="text" name="nombre_programa" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ej: Ingeniería de Sistemas">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Director de Unidad Asignado</label>
                            <select name="id_docente" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Sin Director --</option>
                                @foreach($directores as $director)
                                    <option value="{{ $director->id }}">
                                        {{ $director->name ?? $director->nombre_director ?? $director->username }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow">
                                Guardar Programa
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabla de Programas Existentes -->
                <h3 class="text-lg font-bold mb-4">Programas Académicos Registrados</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Programa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Director de Unidad</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($programas as $programa)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $programa->id_programa }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $programa->nombre_programa }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        @if($programa->directorUnidad)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $programa->directorUnidad->name ?? $programa->directorUnidad->nombre_director }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Sin Asignar
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <form action="{{ route('programas.update', $programa->id_programa) }}" method="POST" class="inline-flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="nombre_programa" value="{{ $programa->nombre_programa }}">
                                            
                                            <select name="id_docente" onchange="this.form.submit()" class="text-xs rounded-md border-gray-300 py-1">
                                                <option value="">Cambiar Director...</option>
                                                @foreach($directores as $director)
                                                    <option value="{{ $director->id }}" {{ ($programa->id_docente == $director->id) ? 'selected' : '' }}>
                                                        {{ $director->name ?? $director->nombre_director }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>

                                        <form action="{{ route('programas.destroy', $programa->id_programa) }}" method="POST" class="inline ml-2" onsubmit="return confirm('¿Seguro de eliminar este programa?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-semibold">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No hay programas académicos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>