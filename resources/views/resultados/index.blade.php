<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 px-4">
        <h2 class="text-3xl font-bold text-center text-[#004d2e] mb-6">Resultados Cuestionario</h2>
        
        <!-- Contenedor del buscador centrado con AlpineJS -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-8"
             x-data="{ 
                query: '{{ old('codigo', request('codigo')) }}', 
                sugerencias: [], 
                async buscarEnTiempoReal() {
                    if (this.query.length < 1) {
                        this.sugerencias = [];
                        return;
                    }
                    try {
                        let response = await fetch(`/resultados-cuestionario/buscar?codigo=${encodeURIComponent(this.query)}&ajax=1`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });
                        if (response.ok) {
                            this.sugerencias = await response.json();
                        }
                    } catch (error) {
                        console.error('Error al obtener sugerencias:', error);
                    }
                }
             }">
             
            <form action="{{ route('resultados.buscar') }}" method="GET" class="flex flex-col items-center">
                <div class="w-full max-w-sm relative">
                    <input type="text" 
                           name="codigo" 
                           x-model="query"
                           @input.debounce.300ms="buscarEnTiempoReal()"
                           @click.away="sugerencias = []"
                           class="w-full form-control" 
                           placeholder="Ingrese ID o Nombre del Estudiante" 
                           required
                           autocomplete="off">

                    <!-- DESPLEGABLE DE AUTOCOMPLETADO FLOTANTE -->
                    <ul x-show="sugerencias.length > 0" 
                        class="absolute z-50 w-full bg-white border border-gray-200 mt-1 rounded-lg shadow-lg text-left divide-y divide-gray-100 overflow-hidden"
                        style="display: none;">
                        <template x-for="estudiante in sugerencias" :key="estudiante.codigo_estudiante">
                            <li>
                                <button type="button"
                                        @click="query = estudiante.codigo_estudiante; sugerencias = []; $nextTick(() => $el.form.submit());"
                                        class="w-full px-4 py-3 hover:bg-gray-50 flex flex-col transition duration-150 text-left">
                                    <span class="font-semibold text-gray-900 text-sm" x-text="estudiante.nombre_estudiante"></span>
                                    <span class="text-xs text-gray-400" x-text="'Código: ' + estudiante.codigo_estudiante"></span>
                                </button>
                            </li>
                        </template>
                    </ul>
                </div>
                <button type="submit" class="mt-4 px-6 py-2 btn-primary">Buscar Respuestas</button>
            </form>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger text-center mb-4 text-red-600 font-semibold">{{ $errors->first() }}</div>
        @endif

        {{-- Al usar return view() desde el controlador cuando falla, $respuestas no existirá, --}}
        {{-- ocultando de manera efectiva la tabla vieja del estudiante anterior --}}
        @if(isset($respuestas))
            <!-- Resultados obtenidos -->
            <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200 mb-8">
                <h3 class="text-xl text-center font-bold text-[#004d2e] mb-6">
                    Respuestas de: {{ $estudiante->nombre_estudiante }} ({{ $estudiante->codigo_estudiante }})
                </h3>
                
                <div class="flex justify-center">
                    <table class="w-full max-w-2xl text-left border-collapse">
                        @foreach($respuestas as $pregunta => $respuesta)
                            <tr class="border-b border-gray-100">
                                <th class="py-3 px-4 text-[#004d2e] capitalize font-semibold">
                                    {{ str_replace('_', ' ', $pregunta) }}
                                </th>
                                <td class="py-3 px-4 text-gray-700 text-right">
                                    {{ $respuesta }}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            <!-- Sección de Ponderación de Riesgo -->
            <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center space-x-2 mb-4 border-b border-gray-100 pb-3">
                    <svg class="w-6 h-6 text-[#f17a28]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-xl font-bold text-[#004d2e]">Metodología y Fórmulas de Ponderación del Riesgo</h3>
                </div>

                <div class="text-sm text-gray-600 space-y-4 leading-relaxed">
                    <p>El sistema cuantifica de forma automatizada la <strong>Ponderación de Condiciones Socio-Educativas</strong> mediante un modelo analítico de puntuación acumulativa...</p>
                    <!-- ... Conservar el resto del texto explicativo igual ... -->
                </div>
            </div>
        @endif
    </div>
</x-app-layout>