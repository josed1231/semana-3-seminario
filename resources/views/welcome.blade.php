<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inicio') }}
        </h2>
    </x-slot>

    <div class="py-10 bg-[#FDFDFC] min-h-[calc(100vh-64px)] flex flex-col justify-center items-center px-4 sm:px-6">
        
        @if(in_array(auth()->user()->rol, ['user', 'estudiante']))
            <!-- ==================== VISTA ESTUDIANTES ==================== -->
            <div class="max-w-3xl w-full bg-white border border-[#e3e3e0] rounded-xl p-6 sm:p-8 shadow-sm">
                
                <!-- Encabezado con saludo personalizado -->
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 pb-6 border-b border-gray-100 text-center sm:text-left">
                    <div class="w-14 h-14 bg-emerald-50 text-[#004d2e] rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-[#004d2e] mb-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Tiempo estimado: 5 minutos
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900">¡Hola, {{ auth()->user()->name }}! 👋</h1>
                        <p class="text-gray-600 text-sm mt-1 leading-relaxed">
                            Bienvenido al sistema de caracterización institucional. Este espacio tiene como objetivo acompañarte en tu vida académica y brindarte el apoyo oportuno que necesites.
                        </p>
                    </div>
                </div>

                <!-- Instructivo Paso a Paso -->
                <div class="py-6">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 text-center sm:text-left">
                        ¿Cómo diligenciar tu caracterización?
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Paso 1 -->
                        <div class="p-4 rounded-lg bg-gray-50 border border-gray-200/70 flex flex-col items-center text-center">
                            <div class="w-8 h-8 bg-[#004d2e] text-white font-bold rounded-full flex items-center justify-center text-sm mb-3">
                                1
                            </div>
                            <h3 class="font-semibold text-gray-800 text-sm mb-1">Información Básica</h3>
                            <p class="text-xs text-gray-500">
                                Selecciona tu programa académico, jornada y el semestre en el que te encuentras.
                            </p>
                        </div>

                        <!-- Paso 2 -->
                        <div class="p-4 rounded-lg bg-gray-50 border border-gray-200/70 flex flex-col items-center text-center">
                            <div class="w-8 h-8 bg-[#004d2e] text-white font-bold rounded-full flex items-center justify-center text-sm mb-3">
                                2
                            </div>
                            <h3 class="font-semibold text-gray-800 text-sm mb-1">Aspectos Generales</h3>
                            <p class="text-xs text-gray-500">
                                Responde brevemente sobre tus condiciones socioeconómicas, laborales y de estilo de vida.
                            </p>
                        </div>

                        <!-- Paso 3 -->
                        <div class="p-4 rounded-lg bg-gray-50 border border-gray-200/70 flex flex-col items-center text-center">
                            <div class="w-8 h-8 bg-[#004d2e] text-white font-bold rounded-full flex items-center justify-center text-sm mb-3">
                                3
                            </div>
                            <h3 class="font-semibold text-gray-800 text-sm mb-1">Factores de Apoyo</h3>
                            <p class="text-xs text-gray-500">
                                Indícanos si requieres acompañamiento académico, psicosocial o de Bienestar.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Nota Informativa y Botón de Acción -->
                <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span>Tus respuestas son totalmente confidenciales y de uso institucional.</span>
                    </div>

                    <a href="{{ route('cuestionario.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-[#004d2e] hover:bg-[#003e1c] rounded-lg shadow-sm transition duration-150">
                        <span>Iniciar Cuestionario</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>

            </div>

        @else
            <!-- ==================== VISTA ADMINISTRATIVOS ==================== -->
            <div class="max-w-3xl w-full bg-white border border-[#e3e3e0] rounded-xl p-8 shadow-sm text-center">
                <div class="inline-flex justify-center items-center w-16 h-16 bg-emerald-50 text-[#004d2e] rounded-full mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-gray-900 mb-2">Panel de Control de Gestión</h1>
                <p class="text-gray-600 mb-8 text-sm max-w-lg mx-auto leading-relaxed">
                    Bienvenido, <strong>{{ auth()->user()->name }}</strong>. Accede a los módulos de monitoreo, análisis de alertas tempranas y gestión académica.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-left">
                    <a href="{{ route('dashboard') }}" class="group p-5 border border-gray-200 hover:border-[#004d2e] rounded-lg transition duration-150 hover:shadow-md bg-white">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-800 group-hover:text-[#004d2e]">Gestión de Alumnos</span>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-[#004d2e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <p class="text-xs text-gray-500">Consulta el listado de estudiantes, directores asignados y niveles de riesgo.</p>
                    </a>

                    <a href="{{ route('resultados.index') }}" class="group p-5 border border-gray-200 hover:border-[#004d2e] rounded-lg transition duration-150 hover:shadow-md bg-white">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-800 group-hover:text-[#004d2e]">Resultados Cuestionarios</span>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-[#004d2e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <p class="text-xs text-gray-500">Visualiza estadísticas globales, métricas y filtros por factores de afectación.</p>
                    </a>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>