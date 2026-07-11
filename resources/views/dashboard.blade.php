<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <style>
        .campus-grid {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 1rem !important;
            margin-bottom: 1rem !important;
        }
        /* Todas las tarjetas ahora comparten el mismo fondo oscuro e idéntica estructura */
        .card-base {
            background-color: #1f2937 !important; 
            border-radius: 0.5rem !important;
            padding: 1.25rem !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1) !important;
        }
        
        /* CORRECCIÓN DE COLORES EN LOS BORDES */
        .card-total { border-left: 5px solid #065f46 !important; } /* Verde Oscuro */
        .card-total p:first-child { color: #34d399 !important; font-weight: 700 !important; }
        
        .card-completadas { border-left: 5px solid #ffffff !important; } /* Blanco */
        .card-completadas p:first-child { color: #ffffff !important; font-weight: 700 !important; }
        
        .card-progreso { border-left: 5px solid #9ca3af !important; } /* Gris Claro */
        .card-progreso p:first-child { color: #e5e7eb !important; font-weight: 700 !important; }
        
        .card-pendientes { border-left: 5px solid #ea580c !important; } /* Naranja */
        .card-pendientes p:first-child { color: #fb923c !important; font-weight: 700 !important; }

        .search-container {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 1rem !important;
            margin-bottom: 1.5rem !important;
        }
    </style>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Grid de Estadísticas Unificado -->
            <div class="campus-grid">
                <!-- Total Tareas - Borde Verde Oscuro -->
                <div class="card-base card-total">
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-400">Total Tareas</p>
                    <p class="text-2xl sm:text-3xl font-black text-white mt-1">{{ $stats['total'] }}</p>
                </div>
                
                <!-- Completadas - CORREGIDO: Ahora es igual a los demás con Borde Blanco -->
                <div class="card-base card-completadas">
                    <p class="text-xs font-bold uppercase tracking-wider text-white">Completadas</p>
                    <p class="text-2xl sm:text-3xl font-black text-white mt-1">{{ $stats['completadas'] }}</p>
                </div>
                
                <!-- En Progreso - Borde Blanco/Gris -->
                <div class="card-base card-progreso">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-200">En Progreso</p>
                    <p class="text-2xl sm:text-3xl font-black text-white mt-1">{{ $stats['en_progreso'] }}</p>
                </div>
                
                <!-- Pendientes - Borde Naranja -->
                <div class="card-base card-pendientes">
                    <p class="text-xs font-bold uppercase tracking-wider text-orange-400">Pendientes</p>
                    <p class="text-2xl sm:text-3xl font-black text-white mt-1">{{ $stats['pendientes'] }}</p>
                </div>
            </div>

            <!-- Promedio por Usuario -->
            <div class="mb-6">
                <div class="bg-gray-850 dark:bg-gray-800 shadow-sm rounded-lg p-4 border-l-4 border-gray-500 flex justify-between items-center">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Promedio por Usuario</p>
                    <p class="text-xl font-bold text-white">{{ $stats['promedio_por_usuario'] }}</p>
                </div>
            </div>

            <!-- Tabla de Tareas -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700">
                <div class="p-6">
                    
                    <div class="search-container">
                        <h3 class="text-base font-bold text-white whitespace-nowrap m-0">
                            Tareas Recientes
                        </h3>
                        
                        <form action="{{ url('/dashboard') }}" method="GET" class="flex items-center gap-2 m-0">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Buscar por ID, título o fecha..." 
                                   class="text-xs rounded-md border-gray-600 bg-white text-black placeholder-gray-500 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm py-1.5 px-3 w-64">
                            
                            <button type="submit" class="inline-flex items-center px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider rounded transition">
                                Buscar
                            </button>
                            @if(request('search'))
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-700 text-gray-200 font-semibold text-xs uppercase rounded hover:bg-gray-600 transition">
                                    Limpiar
                                </a>
                            @endif
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-white">
                            <thead class="text-xs text-gray-300 uppercase bg-gray-700/50 border-b border-gray-700">
                                <tr>
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">Título</th>
                                    <th class="px-4 py-3">Categoría</th>
                                    <th class="px-4 py-3">Estado</th>
                                    <th class="px-4 py-3">Fecha Límite</th>
                                    <th class="px-4 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tareasRecientes as $tarea)
                                <tr class="border-b border-gray-700/50 hover:bg-gray-750/50 transition">
                                    <td class="px-4 py-3 font-semibold text-emerald-400">#{{ $tarea->id }}</td>
                                    <td class="px-4 py-3 text-white font-medium">{{ $tarea->titulo }}</td>
                                    <td class="px-4 py-3 text-gray-200">{{ $tarea->category->nombre ?? 'Sin categoría' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 rounded text-xs font-bold uppercase
                                            @if($tarea->estado == 'completado') bg-emerald-900/60 text-emerald-300 border border-emerald-700
                                            @elseif($tarea->estado == 'en_progreso') bg-blue-900/60 text-blue-300 border border-blue-700
                                            @else bg-amber-900/60 text-amber-300 border border-amber-700 @endif">
                                            {{ str_replace('_', ' ', $tarea->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-200">{{ $tarea->fecha_limite ?? 'Sin fecha' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('tasks.edit', $tarea->id) }}" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 hover:underline">
                                                Editar
                                            </a>
                                            <form action="{{ route('tasks.destroy', $tarea->id) }}" method="POST" 
                                                  onsubmit="return confirm('¿Seguro que deseas eliminar esta tarea?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-bold text-red-400 hover:text-red-300 hover:underline focus:outline-none">
                                                    Borrar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-center text-gray-400 text-xs">No hay tareas registradas.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>