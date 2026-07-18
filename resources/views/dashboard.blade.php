<x-app-layout>
    <x-slot name="header">
        <!-- Contenedor superior estilizado como un rectángulo verde corporativo con texto en blanco -->
        <div class="rounded-2xl p-6 shadow-sm" style="background-color: #004d2e;">
            <h2 class="font-bold text-2xl leading-tight m-0" style="color: #ffffff;">
                {{ __('Sistema de Alertas y Orientación Estudiantil - Cotecnova') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen" style="background-color: #f4f6f8;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Tarjetas de Estadísticas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6" style="border-left: 5px solid #004d2e; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                    <p class="text-xs uppercase font-bold tracking-wider" style="color: #000000;">Total Estudiantes</p>
                    <p class="text-3xl font-black mt-2" style="color: #004d2e;">{{ $statsEstudiantes['total_estudiantes'] }}</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6" style="border-left: 5px solid #ef4444; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                    <p class="text-xs uppercase font-bold tracking-wider" style="color: #ef4444;">Alerta: Riesgo Alto</p>
                    <p class="text-3xl font-black text-red-600 mt-2">{{ $statsEstudiantes['riesgo_alto'] }}</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6" style="border-left: 5px solid #f17a28; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                    <p class="text-xs uppercase font-bold tracking-wider" style="color: #f17a28;">Alerta: Riesgo Medio</p>
                    <p class="text-3xl font-black mt-2" style="color: #f17a28;">{{ $statsEstudiantes['riesgo_medio'] }}</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6" style="border-left: 5px solid #10b981; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                    <p class="text-xs uppercase font-bold tracking-wider" style="color: #10b981;">En Orientación</p>
                    <p class="text-3xl font-black text-emerald-600 mt-2">{{ $statsEstudiantes['con_psicoorientacion'] }}</p>
                </div>
            </div>

            <!-- Contenedor de la Tabla Principal -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-3xl p-6" style="border: 1px solid #e2e8f0;">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
                    <h3 class="text-lg font-bold" style="color: #000000; margin: 0;">Monitoreo de Estudiantes en Alerta</h3>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                        @if(auth()->user()->rol === 'admin' || auth()->user()->rol === 'dir_bienestar')
                            <a href="{{ route('estudiantes.create') }}" 
                               style="background-color: #f17a28; color: #ffffff; text-decoration: none;"
                               onmouseover="this.style.backgroundColor='#d66213'" 
                               onmouseout="this.style.backgroundColor='#f17a28'"
                               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition-colors w-full sm:w-auto shadow-sm cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Registrar Estudiante
                            </a>
                        @endif

                        <form method="GET" action="{{ route('dashboard') }}" class="flex gap-2 w-full sm:w-auto">
                            <input type="text" 
                                   name="search" 
                                   value="{{ $searchTerm ?? '' }}" 
                                   placeholder="Buscar por nombre o código..." 
                                   style="border: 1px solid #cbd5e1; outline: none; transition: border-color 0.15s;"
                                   onfocus="this.style.borderColor='#005a36'; this.style.boxShadow='0 0 0 3px #dcece4';"
                                   onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';"
                                   class="rounded-xl px-4 py-2 text-sm w-full sm:w-64 text-slate-800 bg-white">
                            
                            <button type="submit" 
                                    style="background-color: #004d2e; color: #ffffff;"
                                    onmouseover="this.style.backgroundColor='#002b1a'"
                                    onmouseout="this.style.backgroundColor='#004d2e'"
                                    class="px-5 py-2.5 rounded-xl text-sm font-bold transition-colors cursor-pointer border-none shadow-sm">
                                Buscar
                            </button>

                            @if($searchTerm)
                                <a href="{{ route('dashboard') }}" 
                                   style="background-color: #f1f5f9; color: #000000;"
                                   onmouseover="this.style.backgroundColor='#e2e8f0'"
                                   onmouseout="this.style.backgroundColor='#f1f5f9'"
                                   class="px-4 py-2.5 rounded-xl text-sm font-bold transition-colors text-center shadow-sm">
                                     Limpiar
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Tabla de Estudiantes -->
                <div class="overflow-x-auto rounded-2xl border border-slate-100">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead style="background-color: #f8fafc;">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #000000;">Código</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #000000;">Nombre</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #000000;">Carrera (Programa)</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #000000;">Director de Unidad</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #000000;">Semestre</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #000000;">Jornada</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider" style="color: #000000;">Nivel de Riesgo</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #000000;">Orientación</th>
                                @if(auth()->user()->rol !== 'dir_unidad')
                                    <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider" style="color: #000000;">Acciones</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($estudiantes as $estudiante)
                                <tr class="hover:bg-slate-50/80 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold" style="color: #000000;">
                                        {{ $estudiante->codigo_estudiante }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="font-extrabold" style="color: #000000;">{{ $estudiante->nombre_estudiante }}</div>
                                        <div style="color: #64748b; font-size: 0.75rem;">{{ $estudiante->correo }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #000000;">
                                        {{ $estudiante->programa?->nombre_programa ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #000000;">
                                        {{ $estudiante->directorUnidad?->nombre_director ?? 'Sin asignar' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #000000;">
                                        <!-- Se cambia $estudiante->semestre por la relación correcta de saberesPrevios -->
                                        {{ $estudiante->saberesPrevios?->semestre ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #000000;">
                                        {{ $estudiante->jornada ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        @if($estudiante->riesgo)
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full 
                                                {{ $estudiante->riesgo->nivel_riesgo == 'Alto' ? 'bg-red-50 text-red-700 border border-red-200' : '' }}
                                                {{ $estudiante->riesgo->nivel_riesgo == 'Medio' ? 'bg-orange-50 text-orange-700 border border-orange-200' : '' }}
                                                {{ $estudiante->riesgo->nivel_riesgo == 'Bajo' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}">
                                                {{ $estudiante->riesgo->nivel_riesgo }}
                                            </span>
                                        @else
                                            <span style="color: #94a3b8; font-style: italic;">Sin evaluar</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium" style="color: #000000; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $estudiante->orientacionPsicologica?->observaciones ?? 'Sin orientación' }}">
                                        {{ $estudiante->orientacionPsicologica?->observaciones ?? 'Sin orientación' }}
                                    </td>
                                    @if(auth()->user()->rol !== 'dir_unidad')
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium">
                                            <div class="flex items-center justify-center gap-3.5">
                                                <a href="{{ route('estudiantes.edit', $estudiante->codigo_estudiante) }}" style="color: #005a36;" class="hover:scale-110 transition-transform" title="Editar Registro">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                @if(auth()->user()->rol !== 'psicologo')
                                                    <form action="{{ route('estudiantes.destroy', $estudiante->codigo_estudiante) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este estudiante?');" class="inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" style="color: #ef4444;" class="hover:scale-110 transition-transform bg-transparent border-none p-0 cursor-pointer" title="Eliminar Registro">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1 v3M4 7h16" />
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
                                    <td colspan="{{ auth()->user()->rol !== 'dir_unidad' ? 9 : 8 }}" class="px-6 py-8 whitespace-nowrap text-sm text-center font-bold" style="color: #000000; background-color: #f8fafc;">
                                        No se encontraron estudiantes registrados en el sistema.
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