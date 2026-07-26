<section>
    <header class="mb-4">
        <p class="text-xs text-slate-500 m-0">
            {{ __("Actualiza la información general de tu cuenta, tu dirección de correo electrónico institucional y preferencia de género.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <!-- Nombre Completo -->
        <div>
            <x-input-label for="name" :value="__('Nombre Completo')" class="text-slate-700 font-semibold mb-1" />
            <x-text-input 
                id="name" 
                name="name" 
                type="text" 
                class="mt-1 block w-full rounded-xl border-slate-300 focus:border-[#004d2e] focus:ring-[#004d2e] text-sm text-slate-800" 
                :value="old('name', $user->name)" 
                required 
                autofocus 
                autocomplete="name" 
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Nombre de Usuario / Cédula -->
        <div>
            <x-input-label for="username" :value="__('Nombre de Usuario / Cédula')" class="text-slate-700 font-semibold mb-1" />
            <x-text-input 
                id="username" 
                name="username" 
                type="text" 
                class="mt-1 block w-full rounded-xl border-slate-300 focus:border-[#004d2e] focus:ring-[#004d2e] text-sm text-slate-800" 
                :value="old('username', $user->username)" 
                required 
                autocomplete="username" 
            />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <!-- Correo Electrónico -->
        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" class="text-slate-700 font-semibold mb-1" />
            <x-text-input 
                id="email" 
                name="email" 
                type="email" 
                class="mt-1 block w-full rounded-xl border-slate-300 focus:border-[#004d2e] focus:ring-[#004d2e] text-sm text-slate-800" 
                :value="old('email', $user->email)" 
                required 
                autocomplete="username" 
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            <!-- Verificación de Correo (Si aplica) -->
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="p-4 mt-3 bg-amber-50 border border-amber-200 rounded-xl">
                    <p class="text-xs text-amber-800 flex items-center gap-2 m-0">
                        <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        {{ __('Tu dirección de correo no está verificada.') }}
                    </p>

                    <button form="send-verification" class="mt-2 text-xs text-[#004d2e] font-bold underline hover:text-[#003821] focus:outline-none">
                        {{ __('Haz clic aquí para reenviar el correo de verificación.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-xs text-emerald-700 m-0">
                            {{ __('Se ha enviado un nuevo enlace de verificación a tu correo.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Selector de Género -->
        <div>
            <x-input-label for="genero" :value="__('Género')" class="text-slate-700 font-semibold mb-1" />
            <select 
                id="genero" 
                name="genero" 
                class="mt-1 block w-full rounded-xl border-slate-300 focus:border-[#004d2e] focus:ring-[#004d2e] shadow-sm text-sm text-slate-800 bg-white"
            >
                <option value="" disabled {{ old('genero', $user->genero ?? '') == '' ? 'selected' : '' }}>Seleccione una opción</option>
                <option value="Masculino" {{ old('genero', $user->genero ?? '') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                <option value="Femenino" {{ old('genero', $user->genero ?? '') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                <option value="No Binario" {{ old('genero', $user->genero ?? '') == 'No Binario' ? 'selected' : '' }}>No Binario</option>
                <option value="Otro" {{ old('genero', $user->genero ?? '') == 'Otro' ? 'selected' : '' }}>Otro</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('genero')" />
        </div>

        <!-- Botón de Guardar y Notificación -->
        <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
            <button type="submit" class="px-5 py-2.5 bg-[#004d2e] hover:bg-[#003821] text-white font-bold text-sm rounded-xl shadow-sm transition-all duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('Guardar Cambios') }}
            </button>

            @if (session('status') === 'profile-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-2 rounded-xl border border-emerald-200"
                >
                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Perfil actualizado correctamente.') }}
                </div>
            @endif
        </div>
    </form>
</section>