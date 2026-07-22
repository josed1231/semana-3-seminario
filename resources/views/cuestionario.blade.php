<x-app-layout>
    <div class="py-10 bg-gradient-to-tr from-stone-50 via-green-50/30 to-orange-50/20 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl rounded-3xl p-6 md:p-10 border border-emerald-100">
                
                <!-- Encabezado -->
                <div class="text-center mb-10 border-b border-gray-100 pb-8">
                    <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 tracking-wide uppercase border border-emerald-100">
                        Plataforma Institucional
                    </span>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-3">
                        Formulario de Caracterización Estudiantil (PIAE)
                    </h1>
                </div>

                <form id="form-cuestionario" action="{{ route('cuestionario.store') }}" method="POST" class="space-y-8">
                    @csrf

                    {{-- 
                      Envolvemos todos los campos en un <fieldset>. 
                      Si el usuario NO es un estudiante (es decir, pertenece a los roles administrativos), 
                      se le añade automáticamente el atributo 'disabled', bloqueando todo el formulario de golpe.
                    --}}
                    <fieldset class="space-y-8" {{ in_array(auth()->user()->rol, ['admin', 'psicologo', 'dir_bienestar', 'dir_unidad']) ? 'disabled' : '' }}>

                        <!-- SECCIÓN 1: DATOS ACADÉMICOS -->
                        <div class="bg-emerald-50/40 p-6 md:p-8 rounded-2xl border border-emerald-100/80 space-y-6">
                            <h3 class="text-lg font-bold text-emerald-950 border-b border-emerald-100 pb-3">Información de Registro Académico</h3>
                            
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
                                    @error('trabaja') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 2: CARACTERIZACIÓN -->
                        <div class="p-6 md:p-8 bg-white rounded-2xl border border-gray-200 space-y-6">
                            <h3 class="text-lg font-bold text-slate-800 border-b pb-3">Información Sociodemográfica</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Género: <span class="text-red-500">*</span></label>
                                    <select name="genero" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 py-2.5 text-sm" required>
                                        <option value="" disabled {{ old('genero') ? '' : 'selected' }}>-- Seleccione --</option>
                                        <option value="Hombre" {{ old('genero') == 'Hombre' ? 'selected' : '' }}>Hombre</option>
                                        <option value="Mujer" {{ old('genero') == 'Mujer' ? 'selected' : '' }}>Mujer</option>
                                        <option value="Hombre trans" {{ old('genero') == 'Hombre trans' ? 'selected' : '' }}>Hombre trans</option>
                                        <option value="Mujer trans" {{ old('genero') == 'Mujer trans' ? 'selected' : '' }}>Mujer trans</option>
                                        <option value="No binario" {{ old('genero') == 'No binario' ? 'selected' : '' }}>No binario</option>
                                    </select>
                                    @error('genero') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">¿Víctima del conflicto? <span class="text-red-500">*</span></label>
                                    <select name="victima_confict" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 py-2.5 text-sm" required>
                                        <option value="No" {{ old('victima_confict', 'No') == 'No' ? 'selected' : '' }}>No</option>
                                        <option value="Si" {{ old('victima_confict') == 'Si' ? 'selected' : '' }}>Sí</option>
                                    </select>
                                    @error('victima_confict') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 3: SABERES PREVIOS -->
                        <div id="bloque-saberes-previos" class="hidden p-6 md:p-8 bg-gradient-to-br from-emerald-50/50 to-orange-50/30 rounded-2xl border border-emerald-100 shadow-sm space-y-6">
                            <div class="border-b border-emerald-100 pb-3">
                                <h3 class="text-lg font-bold text-emerald-950 flex items-center gap-2">
                                    <span class="p-1.5 bg-orange-100 text-orange-800 rounded-lg text-sm">✏️</span>
                                    Módulo de Saberes Previos
                                </h3>
                                <p class="text-xs text-emerald-800 mt-1">Este bloque es requerido obligatoriamente para estudiantes de primer semestre.</p>
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

                        <!-- SECCIÓN 4: PONDERACIÓN DE RIESGO Y ESTILO DE VIDA -->
                        <div class="p-6 md:p-8 bg-white rounded-2xl border border-gray-200 space-y-6">
                            <h3 class="text-lg font-bold text-slate-800 border-b pb-3">⚠️ Ponderación de Condiciones Socio-Educativas</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Exigencias académicas: <span class="text-red-500">*</span></label>
                                    <select name="afectacion_academico" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 py-2.5 text-sm" required>
                                        <option value="" disabled {{ old('afectacion_academico') !== null ? '' : 'selected' }}>-- Seleccione --</option>
                                        <option value="0" {{ old('afectacion_academico') == '0' ? 'selected' : '' }}>Sin afectación</option>
                                        <option value="2" {{ old('afectacion_academico') == '2' ? 'selected' : '' }}>Moderada</option>
                                        <option value="4" {{ old('afectacion_academico') == '4' ? 'selected' : '' }}>Alta</option>
                                    </select>
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
                                    @error('afectacion_socioeconomico') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Estrés/Ansiedad: <span class="text-red-500">*</span></label>
                                    <select name="afectacion_psicosocial" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 py-2.5 text-sm" required>
                                        <option value="" disabled {{ old('afectacion_psicosocial') !== null ? '' : 'selected' }}>-- Seleccione --</option>
                                        <option value="0" {{ old('afectacion_psicosocial') == '0' ? 'selected' : '' }}>Casi nunca</option>
                                        <option value="2" {{ old('afectacion_psicosocial') == '2' ? 'selected' : '' }}>Ocasionalmente</option>
                                        <option value="4" {{ old('afectacion_psicosocial') == '4' ? 'selected' : '' }}>Constantemente</option>
                                    </select>
                                    @error('afectacion_psicosocial') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Fila Nueva: Textbox de Actividades Frecuentes -->
                            <div class="space-y-2 pt-2">
                                <label class="block text-sm font-semibold text-gray-700">Actividades más frecuentes (Estilo de vida) <span class="text-red-500">*</span></label>
                                <textarea name="actividad" id="actividad" rows="3" required
                                          placeholder="Describe brevemente las actividades que realizas con más frecuencia fuera de tu jornada académica (ej: practicar algún deporte, cuidar familiares, pasatiempos, etc.)..."
                                          class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 text-sm p-3 placeholder-gray-400 text-gray-900">{{ old('actividad') }}</textarea>
                                <p class="text-xs text-gray-400 mt-1">Esta información nos ayuda a entender tus entornos cotidianos de estilo de vida.</p>
                                @error('actividad') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                    </fieldset>

                    <!-- MANEJO DINÁMICO DEL BOTÓN VS MENSAJE DE VISTA ADMINISTRATIVA -->
                    @if(!in_array(auth()->user()->rol, ['admin', 'psicologo', 'dir_bienestar', 'dir_unidad']))
                        <button type="submit" class="w-full py-4 bg-[#f17a28] text-white font-bold rounded-2xl hover:bg-[#d66213] transition shadow-md">
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
            
            if (selector.value === '1') {
                bloqueSaberes.classList.remove('hidden');
                q1.required = true;
                q2.required = true;
                q3.required = true;
            } else {
                bloqueSaberes.classList.add('hidden');
                q1.required = false;
                q2.required = false;
                q3.required = false;
                
                q1.value = "";
                q2.value = "";
                q3.value = "";
            }
        }

        // Ejecutar al cargar la página por si hay valores previos de 'old()' en caso de error de validación
        document.addEventListener('DOMContentLoaded', function () {
            toggleSaberesPrevios();
        });
    </script>
</x-app-layout>