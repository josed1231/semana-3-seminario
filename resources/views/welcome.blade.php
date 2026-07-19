<x-app-layout>
    <!-- Ranura para el encabezado (opcional, se puede dejar en blanco) -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inicio') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#FDFDFC] min-h-[calc(100vh-64px)] flex flex-col justify-center items-center p-6">
        
        @if(in_array(auth()->user()->rol, ['user', 'estudiante']))
            <!-- ==================== BIENVENIDA ESTUDIANTES ==================== -->
            <div class="max-w-xl w-full text-center bg-white border border-[#e3e3e0] rounded-md p-6 shadow-sm">
                <div class="inline-flex justify-center items-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>

                <h1 class="text-2xl font-semibold mb-2">¡Bienvenido al Sistema!</h1>
                <p class="text-[#706f6c] mb-6 text-sm leading-relaxed">
                    Hola. Has ingresado correctamente a la plataforma. Por favor, dirígete a la sección de cuestionarios en la barra de navegación superior o haz clic en el botón de abajo para comenzar.
                </p>

                <div class="flex justify-center gap-4">
                    <a href="{{ route('cuestionario.create') }}" class="inline-flex items-center justify-center px-5 py-2 text-sm font-medium text-white bg-[#004d2e] hover:bg-[#003e1c] rounded-md transition duration-150">
                        Ir al Cuestionario
                    </a>
                </div>
            </div>
        @else
            <!-- ==================== BIENVENIDA ADMINISTRATIVOS ==================== -->
            <div class="max-w-2xl w-full text-center bg-white border border-[#e3e3e0] rounded-md p-8 shadow-sm">
                <div class="inline-flex justify-center items-center w-16 h-16 bg-green-50 rounded-full mb-4">
                    <svg class="w-8 h-8 text-[#004d2e]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>

                <h1 class="text-2xl font-semibold mb-2">Panel de Control de Gestión</h1>
                <p class="text-[#706f6c] mb-6 text-sm leading-relaxed">
                    Estimado miembro del personal administrativo/psicológico. Desde aquí puede acceder a las herramientas de control, análisis de respuestas de estudiantes y administración general del sistema.
                </p>

                <div class="flex justify-center gap-4 flex-wrap">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-5 py-2 text-sm font-medium text-white bg-[#004d2e] hover:bg-[#003e1c] rounded-md transition duration-150">
                        Ver Gestión de Alumnos
                    </a>
                    <a href="{{ route('resultados.index') }}" class="inline-flex items-center justify-center px-5 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition duration-150">
                        Ver Resultados Cuestionarios
                    </a>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>