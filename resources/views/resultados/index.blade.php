<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 px-4">
        <h2 class="text-3xl font-bold text-center text-[#004d2e] mb-6">Resultados Cuestionario</h2>
        
        <!-- Contenedor del buscador centrado -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-8">
            <form action="{{ route('resultados.buscar') }}" method="GET" class="flex flex-col items-center">
                <div class="w-full max-w-sm">
                    <input type="text" name="codigo" class="w-full form-control" placeholder="Ingrese ID de Estudiante" required>
                </div>
                <button type="submit" class="mt-4 px-6 py-2 btn-primary">Buscar Respuestas</button>
            </form>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger text-center">{{ $errors->first() }}</div>
        @endif

        @if(isset($respuestas))
            <!-- Resultados centrados -->
            <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-xl text-center font-bold text-[#004d2e] mb-6">
                    Respuestas de: {{ $estudiante->nombre_estudiante }} ({{ $estudiante->codigo_estudiante }})
                </h3>
                
                <div class="flex justify-center">
                    <table class="w-full max-w-2xl text-left border-collapse">
                        @foreach($respuestas as $pregunta => $respuesta)
                            <tr class="border-b border-gray-100">
                                <th class="py-3 px-4 text-[#004d2e] capitalize">
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
        @endif
    </div>
</x-app-layout>