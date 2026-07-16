<x-app-layout>
    <div class="py-10 bg-gradient-to-tr from-stone-50 via-green-50/30 to-orange-50/20 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl rounded-3xl p-6 md:p-10 border border-emerald-100">
                
                <!-- Encabezado con la identidad de COTECNOVA -->
                <div class="text-center mb-10 border-b border-gray-100 pb-8">
                    <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 tracking-wide uppercase border border-emerald-100">
                        Plataforma Institucional
                    </span>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-3">
                        Formulario de Caracterización Estudiantil (PIAE)
                    </h1>
                    <p class="text-sm text-gray-500 mt-2 max-w-xl mx-auto">
                        Módulo de Predicción y Alertas Automáticas para la Detección Temprana de la Deserción
                    </p>
                </div>

                <!-- Formulario -->
                <form id="form-cuestionario" action="{{ route('cuestionario.store') }}" method="POST" class="space-y-8">
                    @csrf

                    <!-- SECCIÓN 1: DATOS DE UBICACIÓN ACADÉMICA -->
                    <div class="bg-emerald-50/40 p-6 rounded-2xl border border-emerald-100/80 shadow-sm">
                        <h3 class="text-lg font-bold text-emerald-950 mb-4 flex items-center gap-2">
                            <span class="p-1.5 bg-emerald-100 text-emerald-850 rounded-lg">📊</span>
                            Información de Registro Académico
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="block text-sm font-semibold text-gray-700">Programa Académico:</label>
                                <select name="id_programa" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 bg-white" required>
                                    <option value="" selected disabled>-- Seleccione una opción --</option>
                                    <option value="1">Ingeniería de Sistemas</option>
                                    <option value="2">Tecnología Agropecuaria</option>
                                    <option value="3">Contaduría Pública</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-semibold text-gray-700">Semestre Actual:</label>
                                <select name="semestre" id="semestre" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 bg-white" required onchange="toggleSaberesPrevios()">
                                    <option value="" selected disabled>-- Seleccione una opción --</option>
                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">Semestre {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: CARACTERIZACIÓN GENERAL (ENFOQUE DIFERENCIAL) -->
                    <div class="p-6 bg-white rounded-2xl border border-gray-150 shadow-sm space-y-4">
                        <h3 class="text-lg font-bold text-slate-800 border-b pb-2 flex items-center gap-2">
                            <span class="p-1.5 bg-gray-100 text-gray-700 rounded-lg">📋</span>
                            Caracterización General
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">¿Cómo se identifica en términos de género?</label>
                                <select name="genero" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 bg-white" required>
                                    <option value="" selected disabled>-- Seleccione una opción --</option>
                                    <option value="Hombre">Hombre</option>
                                    <option value="Mujer">Mujer</option>
                                    <option value="Hombre trans">Hombre trans</option>
                                    <option value="Mujer trans">Mujer trans</option>
                                    <option value="No binario">No binario</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">¿Es víctima del conflicto armado?</label>
                                <select name="victima_confict" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 bg-white" required>
                                    <option value="" selected disabled>-- Seleccione una opción --</option>
                                    <option value="No">No</option>
                                    <option value="Si">Sí</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: SABERES PREVIOS (DINÁMICO) -->
                    <div id="bloque-saberes-previos" class="hidden p-6 bg-gradient-to-br from-emerald-50/50 to-orange-50/30 rounded-2xl border border-emerald-100 shadow-sm space-y-6">
                        <div class="border-b border-emerald-100 pb-2">
                            <h3 class="text-lg font-bold text-emerald-950 flex items-center gap-2">
                                <span class="p-1.5 bg-orange-100 text-orange-850 rounded-lg">✏️</span>
                                Módulo de Saberes Previos
                            </h3>
                            <p class="text-xs text-emerald-800 mt-1">Este bloque es requerido obligatoriamente para estudiantes de primer semestre.</p>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-800 leading-relaxed">1. ¿Considera que los contenidos aprendidos en el colegio son suficientes para iniciar su programa?</label>
                                <select name="saberes_colegio" id="saberes_colegio" class="w-full rounded-xl border-gray-300 shadow-sm bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">
                                    <option value="" selected disabled>-- Seleccione una opción --</option>
                                    <option value="Suficientes en su mayoría">Suficientes en su mayoría</option>
                                    <option value="Medianamente suficientes">Medianamente suficientes</option>
                                    <option value="Insuficientes">Insuficientes</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-800 leading-relaxed">2. En lectura y comprensión de textos académicos se siente:</label>
                                <select name="saberes_lectura" id="saberes_lectura" class="w-full rounded-xl border-gray-300 shadow-sm bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">
                                    <option value="" selected disabled>-- Seleccione una opción --</option>
                                    <option value="Muy Competente">Muy Competente</option>
                                    <option value="Competente">Competente</option>
                                    <option value="Necesita mejorar">Necesita mejorar</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-800 leading-relaxed">3. En matemáticas o razonamiento lógico se siente:</label>
                                <select name="saberes_matematicas" id="saberes_matematicas" class="w-full rounded-xl border-gray-300 shadow-sm bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">
                                    <option value="" selected disabled>-- Seleccione una opción --</option>
                                    <option value="Muy Competente">Muy Competente</option>
                                    <option value="Competente">Competente</option>
                                    <option value="Necesita mejorar">Necesita mejorar</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 4: DIMENSIONES DE PONDERACIÓN DE RIESGO -->
                    <div class="p-6 bg-white rounded-2xl border border-gray-150 shadow-sm space-y-6">
                        <h3 class="text-lg font-bold text-slate-800 border-b pb-2 flex items-center gap-2">
                            <span class="p-1.5 bg-orange-100 text-orange-850 rounded-lg">⚠️</span>
                            Ponderación de Condiciones Socio-Educativas
                        </h3>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 leading-relaxed">¿Qué tanto le preocupa no cumplir con las exigencias académicas de la carrera?</label>
                            <select name="afectacion_academico" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 bg-white" required>
                                <option value="" selected disabled>-- Seleccione una opción --</option>
                                <option value="0">No me preocupa / Sin afectación (0 puntos)</option>
                                <option value="2">Preocupación Moderada (2 puntos)</option>
                                <option value="4">Preocupación Alta (4 puntos)</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 leading-relaxed">¿La situación económica de su hogar podría afectar la continuidad de sus estudios?</label>
                            <select name="afectacion_socioeconomico" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 bg-white" required>
                                <option value="" selected disabled>-- Seleccione una opción --</option>
                                <option value="0">No representa un problema (0 puntos)</option>
                                <option value="2">Podría afectarlo de forma leve (2 puntos)</option>
                                <option value="4">Podría afectarlo gravemente (4 puntos)</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 leading-relaxed">¿Ha experimentado cuadros de estrés, desmotivación o ansiedad que interfieran con su rendimiento?</label>
                            <select name="afectacion_psicosocial" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 bg-white" required>
                                <option value="" selected disabled>-- Seleccione una opción --</option>
                                <option value="0">No / Casi nunca (0 puntos)</option>
                                <option value="2">Ocasionalmente (2 puntos)</option>
                                <option value="4">Constantemente (4 puntos)</option>
                            </select>
                        </div>
                    </div>

                    <!-- BOTÓN DE GUARDAR (ESTILO IDÉNTICO A CAMPUSNOVA) -->
                    <div class="text-center pt-8 border-t border-gray-100 mt-8">
                        <button type="submit" 
                                style="background-color: #f17a28;" 
                                onmouseover="this.style.backgroundColor='#d66213'" 
                                onmouseout="this.style.backgroundColor='#f17a28'"
                                class="w-full md:w-auto inline-flex justify-center items-center gap-3 py-3.5 px-12 border border-transparent text-base font-bold rounded-2xl text-white transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-orange-200 shadow-md cursor-pointer">
                            <span>Guardar o Enviar Respuestas</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- CONTROL INTERACTIVO Y VALIDACIÓN DINÁMICA DE CAMPOS OBLIGATORIOS -->
    <script>
        function toggleSaberesPrevios() {
            const selector = document.getElementById('semestre');
            const bloqueSaberes = document.getElementById('bloque-saberes-previos');
            
            // Elementos de saberes previos
            const q1 = document.getElementById('saberes_colegio');
            const q2 = document.getElementById('saberes_lectura');
            const q3 = document.getElementById('saberes_matematicas');
            
            if (selector.value === '1') {
                bloqueSaberes.classList.remove('hidden');
                // Hacemos obligatorios estos campos en primer semestre
                q1.required = true;
                q2.required = true;
                q3.required = true;
            } else {
                bloqueSaberes.classList.add('hidden');
                // Si no es primer semestre, retiramos la obligatoriedad para permitir el envío
                q1.required = false;
                q2.required = false;
                q3.required = false;
                
                // Limpiamos sus valores para que no envíen basura
                q1.value = "";
                q2.value = "";
                q3.value = "";
            }
        }
    </script>
</x-app-layout>