<x-guest-layout>
    <div style="background-color: #ffffff; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2); border-radius: 24px; width: 100%; max-width: 420px; padding: 2.5rem 2rem; box-sizing: border-box; height: auto;">
        
        <!-- Encabezado de la Tarjeta -->
        <div style="margin-bottom: 1.75rem; text-align: center;">
            <h2 style="color: #1e293b; font-weight: 800; font-size: 1.5rem; margin: 0;">Verificación 2FA</h2>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 6px; margin-bottom: 0;">
                Se ha enviado un código de 6 dígitos a tu correo electrónico.
            </p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Formulario de Verificación -->
        <form method="POST" action="{{ route('otp.verify.post') }}" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf

            <!-- Campo de Código OTP -->
            <div>
                <label for="code" style="color: #334155; font-weight: 700; font-size: 0.875rem; display: block; margin-bottom: 6px;">
                    Código de verificación
                </label>
                <input id="code" 
                       type="text" 
                       name="code" 
                       required 
                       autofocus 
                       maxlength="6"
                       placeholder="000000"
                       autocomplete="one-time-code"
                       style="border: 1px solid #cbd5e1; width: 100%; border-radius: 12px; padding: 12px 16px; color: #1e293b; background-color: #ffffff; outline: none; box-sizing: border-box; transition: all 150ms; font-size: 1.3rem; letter-spacing: 6px; text-align: center; font-weight: 700;"
                       onfocus="this.style.borderColor='#005a36'; this.style.boxShadow='0 0 0 4px #dcece4';"
                       onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';" />
                <x-input-error :messages="$errors->get('code')" class="mt-2 text-xs text-red-600" />
            </div>

            <!-- Botones y Enlace Inferior -->
            <div style="border-top: 1px solid #f1f5f9; padding-top: 1.25rem; display: flex; flex-direction: row; align-items: center; justify-content: space-between; gap: 1rem; margin-top: 0.25rem;">
                
                <!-- Botón Reenviar Código (Acción Secundaria) -->
                <button type="submit" 
                        form="resend-form"
                        style="background: transparent; border: none; color: #64748b; font-size: 0.875rem; font-weight: 600; text-decoration: underline; cursor: pointer; padding: 0; transition: color 150ms;"
                        onmouseover="this.style.color='#005a36';"
                        onmouseout="this.style.color='#64748b';">
                    ¿Reenviar código?
                </button>

                <!-- Botón Principal -->
                <button type="submit" 
                        style="background-color: #f17a28; color: #ffffff; border: none; padding: 12px 22px; font-weight: 700; border-radius: 12px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 200ms; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); font-size: 0.875rem;" 
                        onmouseover="this.style.backgroundColor='#d66213'" 
                        onmouseout="this.style.backgroundColor='#f17a28'">
                    <span>Ingresar</span>
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </div>
        </form>

        <!-- Formulario Oculto para Reenvío -->
        <form id="resend-form" method="POST" action="{{ route('otp.resend') }}" style="display: none;">
            @csrf
        </form>
    </div>
</x-guest-layout>