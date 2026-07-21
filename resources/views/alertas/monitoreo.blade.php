<x-app-layout>
    <x-slot name="header">
        <div class="rounded-2xl p-6 shadow-sm bg-[#004d2e]">
            <h2 class="font-bold text-2xl leading-tight m-0 text-white">
                {{ __('Monitoreo y Alertas Estudiantiles - Cotecnova') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen bg-[#f4f6f8]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-md sm:rounded-3xl p-6 border border-slate-200">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
                    <h3 class="text-lg font-bold text-slate-900 m-0">Monitoreo de Estudiantes en Alerta</h3>
                    
                    @if(in_array(auth()->user()->rol, ['admin', 'dir_bienestar']))
                        <a href="{{ route('estudiantes.create') }}" 
                           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold bg-[#f17a28] hover:bg-[#d66213] text-white transition-colors shadow-sm cursor-pointer border-none decoration-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            Registrar Estudiante
                        </a>
                    @endif
                </div>

                <form method="GET" action="{{ route('alertas.monitoreo') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mb-6 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Búsqueda</label>
                        <input type="text" 
                               name="buscar" 
                               value="{{ request('buscar') }}" 
                               placeholder="Nombre o código..." 
                               class="rounded-xl px-3 py-2 text-sm w-full text-slate-800 bg-white border border-slate-300 focus:border-[#005a36] focus:ring-2 focus:ring-[#dcece4] outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Carrera (Programa)</label>
                        <select name="programa" class="rounded-xl px-3 py-2 text-sm w-full text-slate-800 bg-white border border-slate-300 focus:border-[#005a36] focus:ring-2 focus:ring-[#dcece4] outline-none transition-all">
                            <option value="">Todas las carreras</option>
                            @foreach($programas as $prog)
                                <option value="{{ $prog->id_programa }}" {{ request('programa') == $prog->id_programa ? 'selected' : '' }}>
                                    {{ $prog->nombre_programa }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Semestre</label>
                        <select name="semestre" class="rounded-xl px-3 py-2 text-sm w-full text-slate-800 bg-white border border-slate-300 focus:border-[#005a36] focus:ring-2 focus:ring-[#dcece4] outline-none transition-all">
                            <option value="">Todos</option>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ request('semestre') == $i ? 'selected' : '' }}>
                                    Semestre {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jornada</label>
                        <select name="jornada" class="rounded-xl px-3 py-2 text-sm w-full text-slate-800 bg-white border border-slate-300 focus:border-[#005a36] focus:ring-2 focus:ring-[#dcece4] outline-none transition-all">
                            <option value="">Todas</option>
                            <option value="Diurna" {{ request('jornada') == 'Diurna' ? 'selected' : '' }}>Diurna</option>
                            <option value="Nocturna" {{ request('jornada') == 'Nocturna' ? 'selected' : '' }}>Nocturna</option>
                            <option value="Sabatina" {{ request('jornada') == 'Sabatina' ? 'selected' : '' }}>Sabatina</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" 
                                class="flex-1 py-2 px-4 rounded-xl text-sm font-bold bg-[#004d2e] hover:bg-[#002b1a] text-white transition-colors cursor-pointer border-none shadow-sm h-[38px]">
                            Filtrar
                        </button>

                        @if(request()->hasAny(['buscar', 'programa', 'semestre', 'jornada']) && (request('buscar') || request('programa') || request('semestre') || request('jornada')))
                            <a href="{{ route('alertas.monitoreo') }}" 
                               class="py-2 px-3 rounded-xl text-sm font-bold bg-slate-200 hover:bg-slate-300 text-slate-800 transition-colors text-center shadow-sm decoration-none h-[38px] flex items-center justify-center">
                                Limpiar
                            </a>
                        @endif
                    </div>

                </form>

                <div class="overflow-x-auto rounded-2xl border border-slate-100 shadow-sm">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Código</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Nombre</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Carrera (Programa)</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Director de Unidad</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-700">¿Trabaja?</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Semestre</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Jornada</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-700">Nivel de Riesgo</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Actividades</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Orientación</th>
                                
                                {{-- Solo mostrar columna de Acciones a admin y dir_bienestar --}}
                                @if(in_array(auth()->user()->rol, ['admin', 'dir_bienestar']))
                                    <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-700">Acciones</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($estudiantes as $estudiante)
                                <tr class="hover:bg-slate-50/80 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">
                                        {{ $estudiante->codigo_estudiante }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="font-extrabold text-slate-900">{{ $estudiante->nombre_estudiante }}</div>
                                        <div class="text-slate-500 text-xs">{{ $estudiante->correo }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800">
                                        {{ $estudiante->programa?->nombre_programa ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800">
                                        {{ $estudiante->directorUnidad?->nombre_director ?? 'Sin asignar' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($estudiante->trabaja === 'Si')
                                            <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-blue-50 text-blue-700 border border-blue-100">Sí</span>
                                        @elseif($estudiante->trabaja === 'No')
                                            <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-slate-50 text-slate-600 border border-slate-100">No</span>
                                        @else
                                            <span class="text-slate-400 italic">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800">
                                        {{ $estudiante->saberesPrevios?->semestre ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800">
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
                                            <span class="text-slate-400 italic">Sin evaluar</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-slate-800 min-w-[200px] whitespace-normal break-words">
                                        {{ $estudiante->actividades_estilo_vida ?? $estudiante->estiloVida?->actividades_estilo_vida ?? $estudiante->actividad ?? 'Ninguna' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-slate-800 min-w-[220px] whitespace-normal break-words">
                                        {{ $estudiante->orientacionPsicologica?->observaciones ?? 'Sin orientación' }}
                                    </td>
                                    
                                    {{-- Botones de acción limitados por Rol --}}
                                    @if(in_array(auth()->user()->rol, ['admin', 'dir_bienestar']))
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('estudiantes.edit', $estudiante->codigo_estudiante) }}" 
                                                   class="inline-flex items-center justify-center p-2 rounded-xl bg-[#dcece4] hover:bg-[#004d2e] text-[#005a36] hover:text-white shadow-sm transition-all duration-200 group" 
                                                   title="Editar Registro">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 scale-100 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                
                                                @if(auth()->user()->rol === 'admin')
                                                    <form action="{{ route('estudiantes.destroy', $estudiante->codigo_estudiante) }}" 
                                                          method="POST" 
                                                          x-data
                                                          @submit.prevent="if(confirm('¿Estás seguro de que deseas eliminar este estudiante?')) $el.submit();" 
                                                          class="inline m-0">
                                                        @csrf 
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="inline-flex items-center justify-center p-2 rounded-xl bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border-none shadow-sm cursor-pointer transition-all duration-200 group" 
                                                                title="Eliminar Registro">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 scale-100 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
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
                                    <td colspan="{{ in_array(auth()->user()->rol, ['admin', 'dir_bienestar']) ? 11 : 10 }}" class="px-6 py-8 whitespace-nowrap text-sm text-center font-bold text-slate-800 bg-slate-50">
                                        No se encontraron estudiantes registrados con los criterios seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if(method_exists($estudiantes, 'links'))
                    <div class="mt-4">
                        {{ $estudiantes->links() }}
                    </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-md sm:rounded-3xl p-8 border border-slate-200">
                <div class="flex items-center space-x-3 mb-4 border-b border-slate-100 pb-4">
                    <div class="p-2 rounded-xl bg-orange-50 text-[#f17a28]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-[#004d2e] m-0">Metodología y Fórmulas de Ponderación del Riesgo</h3>
                </div>

                <div class="text-sm text-slate-600 space-y-6 leading-relaxed">
                    <p>El sistema PIAE cuantifica de forma automatizada la <strong>Ponderación de Condiciones Socio-Educativas</strong> mediante un modelo analítico matricial de puntuación acumulativa basado en cuatro dimensiones estratégicas:</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="font-bold text-slate-900 block mb-1">📊 Dimensión Académica</span>
                            Mide competencias básicas, regularidad de estudios y el volumen de créditos críticos reprobados u homologados.
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="font-bold text-slate-900 block mb-1">💰 Dimensión Socioeconómica</span>
                            Evalúa el impacto financiero del hogar, dificultades logísticas de desplazamiento y vulnerabilidades de ingresos.
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="font-bold text-slate-900 block mb-1">🧠 Dimensión Psicosocial</span>
                            Monitorea indicadores estables de estrés, desmotivación formativa o interferencias emocionales detectadas.
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="font-bold text-slate-900 block mb-1">🌍 Enfoque Diferencial</span>
                            Considera factores institucionales como género, pertenencia étnica, discapacidades o condición de víctima.
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl bg-emerald-50/60 border border-emerald-100 text-slate-800">
                        <h4 class="font-bold text-emerald-900 text-sm mb-2 flex items-center gap-2">
                            <span>📐 Ecuación del Modelo de Riesgo (Suma Ponderada)</span>
                        </h4>
                        <div class="bg-white p-4 rounded-xl border border-emerald-200 text-center font-bold text-lg text-[#004d2e] shadow-sm my-3 overflow-x-auto select-all">
                            $$Puntaje_{Total} = X_{Acad} + X_{Socioecon} + X_{Psicosoc} + X_{Diferencial}$$
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs text-slate-600 pt-2 border-t border-emerald-100">
                            <div><strong>• Bajo:</strong> 0 - 3 puntos</div>
                            <div><strong>• Medio:</strong> 4 - 7 puntos</div>
                            <div><strong>• Alto:</strong> ≥ 8 puntos</div>
                        </div>
                    </div>

                    <p class="pt-1 text-xs text-slate-500 border-t border-slate-100">
                        * Nota explicativa: La clasificación final del riesgo (<strong>Bajo, Medio, Alto</strong>) se mapea dinámicamente según el total acumulado de criterios y el nivel máximo de afectación registrado en las hojas matrices de seguimiento institucional.
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>