<x-guest-layout>
    <div style="background-color: #ffffff; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2); border-radius: 24px; width: 100%; max-width: 440px; padding: 2.25rem 2rem; box-sizing: border-box; height: auto;">
        
        <div style="margin-bottom: 1.5rem; text-align: center;">
            <h2 style="color: #1e293b; font-weight: 800; font-size: 1.5rem; margin: 0;">Crear Cuenta</h2>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 4px; margin-bottom: 0;">Regístrate para ingresar al sistema</p>
        </div>

        <form method="POST" action="{{ route('register') }}" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf

            <div>
                <label for="name" style="color: #334155; font-weight: 700; font-size: 0.875rem; display: block; margin-bottom: 4px;">Nombre Completo</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus style="border: 1px solid #cbd5e1; width: 100%; border-radius: 12px; padding: 10px 14px; color: #1e293b; background-color: #ffffff; outline: none; box-sizing: border-box;" />
                <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-red-600" />
            </div>

            <div>
                <label for="username" style="color: #334155; font-weight: 700; font-size: 0.875rem; display: block; margin-bottom: 4px;">Nombre de usuario</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required style="border: 1px solid #cbd5e1; width: 100%; border-radius: 12px; padding: 10px 14px; color: #1e293b; background-color: #ffffff; outline: none; box-sizing: border-box;" />
                <x-input-error :messages="$errors->get('username')" class="mt-1 text-xs text-red-600" />
            </div>

            <div>
                <label for="email" style="color: #334155; font-weight: 700; font-size: 0.875rem; display: block; margin-bottom: 4px;">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required style="border: 1px solid #cbd5e1; width: 100%; border-radius: 12px; padding: 10px 14px; color: #1e293b; background-color: #ffffff; outline: none; box-sizing: border-box;" />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-600" />
            </div>

            <div>
                <label for="password" style="color: #334155; font-weight: 700; font-size: 0.875rem; display: block; margin-bottom: 4px;">Contraseña</label>
                <input id="password" type="password" name="password" required style="border: 1px solid #cbd5e1; width: 100%; border-radius: 12px; padding: 10px 14px; color: #1e293b; background-color: #ffffff; outline: none; box-sizing: border-box;" />
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-600" />
            </div>

            <div>
                <label for="password_confirmation" style="color: #334155; font-weight: 700; font-size: 0.875rem; display: block; margin-bottom: 4px;">Confirmar Contraseña</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required style="border: 1px solid #cbd5e1; width: 100%; border-radius: 12px; padding: 10px 14px; color: #1e293b; background-color: #ffffff; outline: none; box-sizing: border-box;" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-red-600" />
            </div>

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
</x-guest-layout>