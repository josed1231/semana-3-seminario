<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Validar correo y contraseña
        $request->authenticate();

        $user = Auth::user();

        // 2. Generar el código OTP
        $otp = $user->generateOtp();

        // 3. Envío protegido con try-catch para evitar errores 504 o caídas si el SMTP falla o se bloquea en Render
        try {
            Mail::send('emails.otp', ['otp' => $otp, 'user' => $user], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Código de verificación - COTECNOVA');
            });
        } catch (\Exception $e) {
            // Si el servidor de Render bloquea el puerto de correo, registramos el error en los logs 
            // pero dejamos que el usuario continúe (puedes ver el OTP en los logs de Render si lo necesitas)
            Log::error('Error al enviar correo OTP: ' . $e->getMessage());
            Log::info('OTP de respaldo para ' . $user->email . ': ' . $otp);
        }

        // 4. Cerrar sesión parcial por seguridad
        Auth::guard('web')->logout();

        // 5. Guardar temporalmente el ID del usuario y la preferencia de recordar sesión
        $request->session()->put('otp_user_id', $user->id);
        $request->session()->put('otp_remember', $request->boolean('remember'));

        // 6. Redirigir al formulario de verificación de código OTP sin bloqueos
        return redirect()->route('otp.verify');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 1. Limpiar el guard de autenticación
        Auth::guard('web')->logout();

        // 2. Invalidar la sesión del usuario
        $request->session()->invalidate();

        // 3. Regenerar el token para seguridad
        $request->session()->regenerateToken();

        // 4. Limpiar cookies específicas si es necesario
        return redirect('/')->withCookie(cookie()->forget('laravel_session'));
    }
}