<x-app-layout>
    {{-- Protección visual de nivel de vista para excluir a dir_unidad y perfiles no autorizados --}}
    @if(!in_array(auth()->user()->rol, ['admin', 'psicologo', 'dir_bienestar']))
        @php abort(403, 'No tienes permisos para acceder a esta vista.'); @endphp
    @endif

    <div class="max-w-4xl mx-auto py-10 px-4">
        <h2 class="text-3xl font-bold text-center text-[#004d2e] mb-6">Resultados Cuestionario</h2>
        
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
                           placeholder="Ingrese Cédula, ID o Nombre" 
                           required
                           autocomplete="off">

                    <ul x-show="sugerencias.length > 0" 
                        class="absolute z-50 w-full bg-white border border-gray-200 mt-1 rounded-lg shadow-lg text-left divide-y divide-gray-100 overflow-hidden"
                        style="display: none;">
                        <template x-for="estudiante in sugerencias" :key="estudiante.codigo_estudiante">
                            <li>
                                <button type="button"
                                        @click="query = estudiante.codigo_estudiante; sugerencias = []; $nextTick(() => $el.form.submit());"
                                        class="w-full px-4 py-3 hover:bg-gray-50 flex flex-col transition duration-150 text-left">
                                    <span class="font-semibold text-gray-900 text-sm" x-text="estudiante.nombre_estudiante"></span>
                                    <span class="text-xs text-gray-400" x-text="'Código/Cédula: ' + (estudiante.cedula ?? estudiante.codigo_estudiante)"></span>
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

        {{-- Mostrar resultados si existen --}}
        @if(isset($respuestas))
            <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200 mb-8">
                <h3 class="text-xl text-center font-bold text-[#004d2e] mb-6">
                    Respuestas de: {{ $estudiante->nombre_estudiante }} ({{ $estudiante->codigo_estudiante }})
                </h3>
                
                <div class="flex justify-center">
                    <table class="w-full max-w-2xl text-left border-collapse">
                        @foreach($respuestas as $pregunta => $respuesta)
                            {{-- Omitir la clave "actividad" para evitar duplicado con "actividades_estilo_vida" --}}
                            @if($pregunta === 'actividad')
                                @continue
                            @endif

                            <tr class="border-b border-gray-100">
                                <th class="py-3 px-4 text-[#004d2e] capitalize font-semibold">
                                    {{ str_replace('_', ' ', $pregunta) }}
                                </th>
                                <td class="py-3 px-4 text-gray-700 text-right">
                                    {{ is_array($respuesta) ? implode(', ', $respuesta) : $respuesta }}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>