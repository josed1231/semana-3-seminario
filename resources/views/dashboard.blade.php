<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sistema de Alertas y Orientación Estudiantil - Cotecnova') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500 dark:text-gray-400 uppercase font-bold">Total Estudiantes</p>
                    <p class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 mt-2">{{ $statsEstudiantes['total_estudiantes'] }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <p class="text-sm text-gray-500 dark:text-gray-400 uppercase font-bold">Alerta: Riesgo Alto</p>
                    <p class="text-3xl font-extrabold text-red-600 dark:text-red-400 mt-2">{{ $statsEstudiantes['riesgo_alto'] }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <p class="text-sm text-gray-500 dark:text-gray-400 uppercase font-bold">Alerta: Riesgo Medio</p>
                    <p class="text-3xl font-extrabold text-yellow-600 dark:text-yellow-450 mt-2">{{ $statsEstudiantes['riesgo_medio'] }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <p class="text-sm text-gray-500 dark:text-gray-400 uppercase font-bold">En Orientación</p>
                    <p class="text-3xl font-extrabold text-green-600 dark:text-green-400 mt-2">{{ $statsEstudiantes['con_psicoorientacion'] }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Monitoreo de Estudiantes en Alerta</h3>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                        @if(auth()->user()->rol === 'admin' || auth()->user()->rol === 'dir_bienestar')
                            <a href="{{ route('estudiantes.create') }}" class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-semibold transition-colors w-full sm:w-auto shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Registrar Estudiante
                            </a>
                        @endif

                        <form method="GET" action="{{ route('dashboard') }}" class="flex gap-2 w-full sm:w-auto">
                            <input type="text" name="search" value="{{ $searchTerm ?? '' }}" placeholder="Buscar por nombre o código..." class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm w-full sm:w-64">
                            <button type="submit" class="bg-gray-800 dark:bg-gray-700 hover:bg-gray-700 dark:hover:bg-gray-650 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">Buscar</button>
                            @if($searchTerm)
                                <a href="{{ route('dashboard') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-md text-sm font-medium transition-colors">Limpiar</a>
                            @endif
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-150 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Código</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Carrera (Programa)</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Docente Tutor</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Promedio</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hrs. Estudio</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Nivel de Riesgo</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Orientación</th>
                                @if(auth()->user()->rol !== 'dir_unidad')
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($estudiantes as $estudiante)
                                <tr class="border-b border-transparent hover:border-white/45 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $estudiante->codigo_estudiante }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="font-bold text-gray-800 dark:text-gray-200">{{ $estudiante->nombre_estudiante }}</div>
                                        <div class="text-xs text-gray-450 dark:text-gray-400">{{ $estudiante->correo }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-550 dark:text-gray-300">{{ $estudiante->programa->nombre_programa }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-550 dark:text-gray-300">{{ $estudiante->docente->nombre_docente }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="font-extrabold {{ $estudiante->promedio >= 4.0 ? 'text-green-600 dark:text-green-400' : 'text-red-500' }}">
                                            {{ $estudiante->promedio }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-350">
                                        {{ $estudiante->estiloVida->horas_estudio_semanal ?? '0' }} hrs
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        @if($estudiante->riesgo)
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full 
                                                {{ $estudiante->riesgo->nivel_riesgo == 'Alto' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                                {{ $estudiante->riesgo->nivel_riesgo == 'Medio' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                                {{ $estudiante->riesgo->nivel_riesgo == 'Bajo' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}">
                                                {{ $estudiante->riesgo->nivel_riesgo }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">Sin evaluar</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-550 dark:text-gray-300">
                                        {{ $estudiante->orientacionPsicologica->observaciones ?? 'Sin orientación' }}
                                    </td>
                                    @if(auth()->user()->rol !== 'dir_unidad')
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium">
                                            <div class="flex items-center justify-center gap-3">
                                                <a href="{{ route('estudiantes.edit', $estudiante->codigo_estudiante) }}" class="text-blue-500 hover:text-blue-400 transition-colors" title="Editar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                
                                                @if(auth()->user()->rol === 'admin')
                                                    <form action="{{ route('estudiantes.destroy', $estudiante->codigo_estudiante) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este estudiante?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-400 transition-colors" title="Eliminar">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->rol !== 'dir_unidad' ? 9 : 8 }}" class="px-6 py-4 whitespace-nowrap text-sm text-gray-550 text-center dark:text-gray-400">No se encontraron estudiantes registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>