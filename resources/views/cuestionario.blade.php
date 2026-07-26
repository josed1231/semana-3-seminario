<x-app-layout>
    <div class="py-10 bg-slate-50/50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-3xl p-6 md:p-10 border border-gray-100">
                
                <!-- Encabezado -->
                <div class="text-center mb-10 border-b border-gray-100 pb-8">
                    <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 tracking-wide uppercase border border-emerald-100">
                        Plataforma Institucional
                    </span>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-3">
                        Formulario de Caracterización Estudiantil (PIAE)
                    </h1>
                    <p class="text-sm text-gray-500 mt-2 max-w-2xl mx-auto">
                        La información registrada nos ayuda a orientar nuestros programas de tutoría, bienestar y apoyo integral.
                    </p>
                </div>

                <form id="form-cuestionario" action="{{ route('cuestionario.store') }}" method="POST" class="space-y-8">
                    @csrf

                    <fieldset class="space-y-8" {{ in_array(auth()->user()->rol, ['admin', 'psicologo', 'dir_bienestar', 'dir_unidad']) ? 'disabled' : '' }}>

                        <!-- SECCIÓN 1: DATOS ACADÉMICOS -->
                        <div class="bg-emerald-50/30 p-6 md:p-8 rounded-2xl border border-emerald-100 space-y-6">
                            <div>
                                <h3 class="text-lg font-bold text-emerald-950">Información de Registro Académico</h3>
                                <p class="text-xs text-emerald-800 mt-1">
                                    Permite relacionar tus respuestas con tu programa y coordinar apoyos con tu dirección académica.
                                </p>
                            </div>
                            
                            <!-- Fila 1: Programa, Semestre, Jornada -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Programa Académico: <span class="text-red-500">*</span></label>
                                    <select name="id_programa" id="id_programa" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 py-2.5 text-sm" required>
                                        <option value="" disabled {{ old('id_programa') ? '' : 'selected' }}>-- Seleccione su programa --</option>
                                        @foreach($programas as $prog)
                                            <option value="{{ $prog->id_programa }}" {{ old('id_programa') == $prog->id_programa ? 'selected' : '' }}>
                                                {{ $prog->nombre_programa }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_programa') 
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Semestre Actual: <span class="text-red-500">*</span></label>
                                    <select name="semestre" id="semestre" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 py-2.5 text-sm" required onchange="toggleSaberesPrevios()">
                                        <option value="" disabled {{ old('semestre') ? '' : 'selected' }}>-- Seleccione --</option>
                                        @for ($i = 1; $i <= 10; $i++) 
                                            <option value="{{ $i }}" {{ old('semestre') == $i ? 'selected' : '' }}>Semestre {{ $i }}</option> 
                                        @endfor
                                    </select>
                                    @error('semestre') 
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Jornada: <span class="text-red-500">*</span></label>
                                    <select name="jornada" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 py-2.5 text-sm" required>
                                        <option value="" disabled {{ old('jornada') ? '' : 'selected' }}>-- Seleccione --</option>
                                        <option value="Diurna" {{ old('jornada') == 'Diurna' ? 'selected' : '' }}>Diurna</option>
                                        <option value="Nocturna" {{ old('jornada') == 'Nocturna' ? 'selected' : '' }}>Nocturna</option>
                                        <option value="Sabatina" {{ old('jornada') == 'Sabatina' ? 'selected' : '' }}>Sabatina</option>
                                    </select>
                                    @error('jornada') 
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>

                            <!-- Fila 2: Pregunta Situación Laboral -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">¿Actualmente trabaja? <span class="text-red-500">*</span></label>
                                    <select name="trabaja" id="trabaja" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 py-2.5 text-sm text-gray-900" required>
                                        <option value="" disabled {{ old('trabaja') ? '' : 'selected' }}>-- Seleccione --</option>
                                        <option value="Si" {{ old('trabaja') == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ old('trabaja') == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                    <p class="text-xs text-gray-500">Nos permite sugerir horarios flexibles de tutorías en caso de cruce de jornada.</p>
                                    @error('trabaja') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 2: CARACTERIZACIÓN (OPCIONES DE GÉNERO UNIFICADAS) -->
                        <div class="p-6 md:p-8 bg-white rounded-2xl border border-gray-200 space-y-6">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Información Sociodemográfica</h3>
                                <p class="text-xs text-gray-500 mt-1">
                                    Información confidencial para fines de inclusión y convocatorias institucionales.
                                </p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Género: <span class="text-red-500">*</span></label>
                                    <select name="genero" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 py-2.5 text-sm" required>
                                        <option value="" disabled {{ old('genero') ? '' : 'selected' }}>-- Seleccione --</option>
                                        <option value="Masculino" {{ old('genero') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                        <option value="Femenino" {{ old('genero') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                        <option value="No binario" {{ old('genero') == 'No binario' ? 'selected' : '' }}>No binario</option>
                                        <option value="Otro" {{ old('genero') == 'Otro' ? 'selected' : '' }}>Otro / Prefiero no decir</option>
                                    </select>
                                    @error('genero') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">¿Víctima del conflicto? <span class="text-red-500">*</span></label>
                                    <select name="victima_confict" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 py-2.5 text-sm" required>
                                        <option value="No" {{ old('victima_confict', 'No') == 'No' ? 'selected' : '' }}>No</option>
                                        <option value="Si" {{ old('victima_confict') == 'Si' ? 'selected' : '' }}>Sí</option>
                                    </select>
                                    <p class="text-xs text-gray-500">Para facilitar la vinculación a convocatorias de subsidios y ayudas.</p>
                                    @error('victima_confict') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 3: SABERES PREVIOS -->
                        <div id="bloque-saberes-previos" class="hidden p-6 md:p-8 bg-gradient-to-br from-emerald-50/40 to-slate-50 rounded-2xl border border-emerald-100 shadow-sm space-y-6">
                            <div class="border-b border-emerald-100 pb-3">
                                <h3 class="text-lg font-bold text-emerald-950 flex items-center gap-2">
                                    <span>📚</span>
                                    Módulo de Saberes Previos
                                </h3>
                                <p class="text-xs text-emerald-800 mt-1">Este bloque es requerido obligatoriamente para estudiantes de primer semestre con el fin de proyectar talleres de nivelación.</p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex flex-col justify-between space-y-2">
                                    <label class="block text-sm font-medium text-gray-800 leading-relaxed">1. ¿Considera que los contenidos aprendidos en el colegio son suficientes para iniciar su programa?</label>
                                    <select name="saberes_colegio" id="saberes_colegio" class="w-full rounded-xl border-gray-300 shadow-sm bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 py-2.5 text-sm">
                                        <option value="" disabled {{ old('saberes_colegio') ? '' : 'selected' }}>-- Seleccione una opción --</option>
                                        <option value="Suficientes en su mayoría" {{ old('saberes_colegio') == 'Suficientes en su mayoría' ? 'selected' : '' }}>Suficientes en su mayoría</option>
                                        <option value="Medianamente suficientes" {{ old('saberes_colegio') == 'Medianamente suficientes' ? 'selected' : '' }}>Medianamente suficientes</option>
                                        <option value="Insuficientes" {{ old('saberes_colegio') == 'Insuficientes' ? 'selected' : '' }}>Insuficientes</option>
                                    </select>
                                </div>

                                <div class="flex flex-col justify-between space-y-2">
                                    <label class="block text-sm font-medium text-gray-800 leading-relaxed">2. En lectura y comprensión de textos académicos se siente:</label>
                                    <select name="saberes_lectura" id="saberes_lectura" class="w-full rounded-xl border-gray-300 shadow-sm bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 py-2.5 text-sm">
                                        <option value="" disabled {{ old('saberes_lectura') ? '' : 'selected' }}>-- Seleccione una opción --</option>
                                        <option value="Muy Competente" {{ old('saberes_lectura') == 'Muy Competente' ? 'selected' : '' }}>Muy Competente</option>
                                        <option value="Competente" {{ old('saberes_lectura') == 'Competente' ? 'selected' : '' }}>Competente</option>
                                        <option value="Necesita mejorar" {{ old('saberes_lectura') == 'Necesita mejorar' ? 'selected' : '' }}>Necesita mejorar</option>
                                    </select>
                                </div>

                                <div class="flex flex-col justify-between space-y-2 md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-800 leading-relaxed">3. En matemáticas o razonamiento lógico se siente:</label>
                                    <select name="saberes_matematicas" id="saberes_matematicas" class="w-full rounded-xl border-gray-300 shadow-sm bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 py-2.5 text-sm">
                                        <option value="" disabled {{ old('saberes_matematicas') ? '' : 'selected' }}>-- Seleccione una opción --</option>
                                        <option value="Muy Competente" {{ old('saberes_matematicas') == 'Muy Competente' ? 'selected' : '' }}>Muy Competente</option>
                                        <option value="Competente" {{ old('saberes_matematicas') == 'Competente' ? 'selected' : '' }}>Competente</option>
                                        <option value="Necesita mejorar" {{ old('saberes_matematicas') == 'Necesita mejorar' ? 'selected' : '' }}>Necesita mejorar</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 4: FACTORES DE ACOMPAÑAMIENTO Y ESTILO DE VIDA -->
                        <div class="p-6 md:p-8 bg-white rounded-2xl border border-gray-200 space-y-6">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800 border-b pb-3">Factores de Acompañamiento y Estilo de Vida</h3>
                                <p class="text-xs text-gray-500 mt-2">
                                    Indica tu apreciación en cada área para enfocar las estrategias de soporte y bienestar institucional.
                                </p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Dificultad con exigencias académicas: <span class="text-red-500">*</span></label>
                                    <select name="afectacion_academico" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 py-2.5 text-sm" required>
                                        <option value="" disabled {{ old('afectacion_academico') !== null ? '' : 'selected' }}>-- Seleccione --</option>
                                        <option value="0" {{ old('afectacion_academico') == '0' ? 'selected' : '' }}>Sin afectación</option>
                                        <option value="2" {{ old('afectacion_academico') == '2' ? 'selected' : '' }}>Moderada</option>
                                        <option value="4" {{ old('afectacion_academico') == '4' ? 'selected' : '' }}>Alta</option>
                                    </select>
                                    <p class="text-xs text-gray-500">Para coordinar refuerzos o tutorías temáticas.</p>
                                    @error('afectacion_academico') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Situación económica: <span class="text-red-500">*</span></label>
                                    <select name="afectacion_socioeconomico" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 py-2.5 text-sm" required>
                                        <option value="" disabled {{ old('afectacion_socioeconomico') !== null ? '' : 'selected' }}>-- Seleccione --</option>
                                        <option value="0" {{ old('afectacion_socioeconomico') == '0' ? 'selected' : '' }}>No representa problema</option>
                                        <option value="2" {{ old('afectacion_socioeconomico') == '2' ? 'selected' : '' }}>Afectación leve</option>
                                        <option value="4" {{ old('afectacion_socioeconomico') == '4' ? 'selected' : '' }}>Afectación grave</option>
                                    </select>
                                    <p class="text-xs text-gray-500">Para la gestión de beneficios o apoyos alimentarios/transporte.</p>
                                    @error('afectacion_socioeconomico') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Manejo de Estrés / Ansiedad: <span class="text-red-500">*</span></label>
                                    <select name="afectacion_psicosocial" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 py-2.5 text-sm" required>
                                        <option value="" disabled {{ old('afectacion_psicosocial') !== null ? '' : 'selected' }}>-- Seleccione --</option>
                                        <option value="0" {{ old('afectacion_psicosocial') == '0' ? 'selected' : '' }}>Casi nunca</option>
                                        <option value="2" {{ old('afectacion_psicosocial') == '2' ? 'selected' : '' }}>Ocasionalmente</option>
                                        <option value="4" {{ old('afectacion_psicosocial') == '4' ? 'selected' : '' }}>Constantemente</option>
                                    </select>
                                    <p class="text-xs text-gray-500">Para ofrecer orientación con Bienestar Universitario.</p>
                                    @error('afectacion_psicosocial') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Textbox de Actividades Frecuentes -->
                            <div class="space-y-2 pt-2">
                                <label class="block text-sm font-semibold text-gray-700">Actividades más frecuentes (Estilo de vida) <span class="text-red-500">*</span></label>
                                <textarea name="actividad" id="actividad" rows="3" required
                                          placeholder="Describe brevemente las actividades que realizas con más frecuencia fuera de tu jornada académica (ej: practicar algún deporte, cuidar familiares, pasatiempos, compromisos laborales, etc.)..."
                                          class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 text-sm p-3 placeholder-gray-400 text-gray-900">{{ old('actividad') }}</textarea>
                                <p class="text-xs text-gray-500">Nos ayuda a adaptar las actividades culturales, deportivas e institucionales a los horarios de la comunidad.</p>
                                @error('actividad') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                    </fieldset>

                    <!-- MANEJO DINÁMICO DEL BOTÓN VS MENSAJE DE VISTA ADMINISTRATIVA -->
                    @if(!in_array(auth()->user()->rol, ['admin', 'psicologo', 'dir_bienestar', 'dir_unidad']))
                        <button type="submit" class="w-full py-4 bg-[#f17a28] text-white font-bold rounded-xl hover:bg-[#d66213] transition shadow-md text-base">
                            Guardar Respuestas
                        </button>
                    @else
                        <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl flex items-center gap-3 shadow-sm">
                            <span class="text-xl">👁️</span>
                            <div class="text-sm">
                                <p class="font-bold">Modo de Vista Previa</p>
                                <p class="text-amber-700/90 font-medium">Como personal de gestión/psicología, puedes revisar la estructura y campos del formulario, pero las acciones de guardado se encuentran deshabilitadas.</p>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- CONTROL INTERACTIVO Y VALIDACIÓN DINÁMICA -->
    <script>
        function toggleSaberesPrevios() {
            const selector = document.getElementById('semestre');
            const bloqueSaberes = document.getElementById('bloque-saberes-previos');
            
            const q1 = document.getElementById('saberes_colegio');
            const q2 = document.getElementById('saberes_lectura');
            const q3 = document.getElementById('saberes_matematicas');
            
            if (selector && selector.value === '1') {
                bloqueSaberes.classList.remove('hidden');
                if (q1) q1.required = true;
                if (q2) q2.required = true;
                if (q3) q3.required = true;
            } else if (bloqueSaberes) {
                bloqueSaberes.classList.add('hidden');
                if (q1) { q1.required = false; q1.value = ""; }
                if (q2) { q2.required = false; q2.value = ""; }
                if (q3) { q3.required = false; q3.value = ""; }
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            toggleSaberesPrevios();
        });
    </script>
</x-app-layout>