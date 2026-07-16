<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold mb-5 text-[#004d2e]">Búsqueda de Estudiante</h2>
        
        <form action="{{ route('psicologo.index') }}" method="GET" class="bg-white p-6 rounded-lg shadow mb-6">
            <input type="text" name="id_estudiante" placeholder="Ingrese ID del estudiante..." 
                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#005a36]" 
                   value="{{ request('id_estudiante') }}">
            <button type="submit" class="mt-4 btn-primary">Buscar Respuestas</button>
        </form>

        @if(isset($estudiante))
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-bold mb-4 text-[#004d2e]">Datos del Estudiante</h3>
                <p><strong>Nombre:</strong> {{ $estudiante->name }}</p>
                <p><strong>Correo:</strong> {{ $estudiante->email }}</p>
                
                <h4 class="mt-6 font-bold text-[#004d2e]">Respuestas del Cuestionario:</h4>
                <div class="mt-2">
                    @forelse($estudiante->respuestas as $respuesta)
                        <div class="border-b py-2">
                            <p><strong>Pregunta:</strong> {{ $respuesta->pregunta }}</p>
                            <p><strong>Respuesta:</strong> {{ $respuesta->valor }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500">No hay respuestas registradas para este estudiante.</p>
                    @endforelse
                </div>
            </div>
        @elseif(request()->has('id_estudiante'))
            <p class="text-red-600 font-bold mt-4">No se encontró un estudiante con ese ID.</p>
        @endif
    </div>
</x-app-layout>