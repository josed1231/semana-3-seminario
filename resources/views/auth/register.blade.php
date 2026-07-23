<x-guest-layout>
    <div style="background-color: #ffffff; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2); border-radius: 24px; width: 100%; max-width: 440px; padding: 2.25rem 2rem; box-sizing: border-box; height: auto;">
        
        <div style="margin-bottom: 1.5rem; text-align: center;">
            <h2 style="color: #1e293b; font-weight: 800; font-size: 1.5rem; margin: 0;">Crear Cuenta</h2>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 4px; margin-bottom: 0;">Regístrate para ingresar al sistema</p>
        </div>

        <form method="POST" action="{{ route('register') }}" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf

            <!-- Nombre Completo -->
            <div>
                <label for="name" style="color: #334155; font-weight: 700; font-size: 0.875rem; display: block; margin-bottom: 4px;">Nombre Completo</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus style="border: 1px solid #cbd5e1; width: 100%; border-radius: 12px; padding: 10px 14px; color: #1e293b; background-color: #ffffff; outline: none; box-sizing: border-box;" />
                <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-red-600" />
            </div>

            <!-- Cédula (Antes Nombre de usuario) -->
            <div>
                <label for="username" style="color: #334155; font-weight: 700; font-size: 0.875rem; display: block; margin-bottom: 4px;">Cédula</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" placeholder="Número de cédula" required style="border: 1px solid #cbd5e1; width: 100%; border-radius: 12px; padding: 10px 14px; color: #1e293b; background-color: #ffffff; outline: none; box-sizing: border-box;" />
                <x-input-error :messages="$errors->get('username')" class="mt-1 text-xs text-red-600" />
            </div>

            <!-- Correo electrónico -->
            <div>
                <label for="email" style="color: #334155; font-weight: 700; font-size: 0.875rem; display: block; margin-bottom: 4px;">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required style="border: 1px solid #cbd5e1; width: 100%; border-radius: 12px; padding: 10px 14px; color: #1e293b; background-color: #ffffff; outline: none; box-sizing: border-box;" />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-600" />
            </div>

            <!-- Contraseña con Ojo -->
            <div>
                <label for="password" style="color: #334155; font-weight: 700; font-size: 0.875rem; display: block; margin-bottom: 4px;">Contraseña</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input id="password" type="password" name="password" required style="border: 1px solid #cbd5e1; width: 100%; border-radius: 12px; padding: 10px 42px 10px 14px; color: #1e293b; background-color: #ffffff; outline: none; box-sizing: border-box;" />
                    <button type="button" 
                            onclick="togglePassword('password', 'eye-icon-pass')" 
                            style="position: absolute; right: 12px; background: transparent; border: none; cursor: pointer; color: #64748b; padding: 0; display: flex; align-items: center; justify-content: center;"
                            tabindex="-1">
                        <svg id="eye-icon-pass" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-600" />
            </div>

            <!-- Confirmar Contraseña con Ojo -->
            <div>
                <label for="password_confirmation" style="color: #334155; font-weight: 700; font-size: 0.875rem; display: block; margin-bottom: 4px;">Confirmar Contraseña</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input id="password_confirmation" type="password" name="password_confirmation" required style="border: 1px solid #cbd5e1; width: 100%; border-radius: 12px; padding: 10px 42px 10px 14px; color: #1e293b; background-color: #ffffff; outline: none; box-sizing: border-box;" />
                    <button type="button" 
                            onclick="togglePassword('password_confirmation', 'eye-icon-confirm')" 
                            style="position: absolute; right: 12px; background: transparent; border: none; cursor: pointer; color: #64748b; padding: 0; display: flex; align-items: center; justify-content: center;"
                            tabindex="-1">
                        <svg id="eye-icon-confirm" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-red-600" />
            </div>

            <!-- Botones -->
            <div style="border-top: 1px solid #f1f5f9; padding-top: 1rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-top: 0.5rem;">
                <a href="{{ route('login') }}" style="color: #64748b; font-size: 0.875rem; font-weight: 600; text-decoration: underline;">
                    ¿Ya estás registrado?
                </a>

                <button type="submit" style="background-color: #f17a28; color: #ffffff; border: none; padding: 10px 20px; font-weight: 700; border-radius: 12px; cursor: pointer; font-size: 0.875rem;" onmouseover="this.style.backgroundColor='#d66213'" onmouseout="this.style.backgroundColor='#f17a28'">
                    Registrarse
                </button>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24M1 1l22 22" />`;
            } else {
                input.type = 'password';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
            }
        }
    </script>
</x-guest-layout>