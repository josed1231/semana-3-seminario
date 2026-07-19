<x-app-layout>
    <div class="py-12 text-center">
        <div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-lg border border-gray-100">
            <!-- Icono de éxito -->
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 text-green-600 rounded-full text-3xl mb-4 font-bold">✓</div>
            
            <h1 class="text-2xl font-bold text-gray-800">¡Registro Completado!</h1>
            <p class="text-gray-500 mt-2">Tus respuestas han sido procesadas exitosamente por el módulo de alertas de COTECNOVA.</p>
            
            <!-- Separador visual -->
            <hr class="my-6 border-gray-100">

            <!-- NUEVO: Botón de retorno al inicio/bienvenida -->
            <div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center w-full px-5 py-2.5 text-sm font-medium text-white bg-[#004d2e] hover:bg-[#003e1c] rounded-lg shadow-sm transition-colors duration-150 ease-in-out">
                    Volver al Inicio
                </a>
            </div>
        </div>
    </div>
</x-app-layout>