<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OtpVerificationController extends Controller
{
    /**
     * Muestra la pantalla para ingresar el código de 6 dígitos.
     */
    public function show(Request $request)
    {
        if (!$request->session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $user = User::find($request->session()->get('otp_user_id'));
        if (!$user) {
            return redirect()->route('login');
        }

        return view('auth.otp-verify');
    }

    /**
     * Valida el código OTP ingresado por el usuario.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = $request->session()->get('otp_user_id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->verifyOtp($request->code)) {
            return back()->withErrors(['code' => 'El código ingresado no es válido o ha expirado.']);
        }

        // Limpiar OTP para evitar reutilización
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        // Completar sesión de usuario
        $request->session()->forget('otp_user_id');
        Auth::loginUsingId($user->id, $request->session()->get('otp_remember', false));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Reenvía un nuevo código OTP en caso de que expire o no llegue.
     */
    public function resend(Request $request)
    {
        $userId = $request->session()->get('otp_user_id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login');
        }

        try {
            $otp = $user->generateOtp();
            Mail::to($user->email)->send(new OtpMail($otp, $user));

            return back()->with('status', 'Se ha enviado un nuevo código a tu correo.');
        } catch (\Exception $e) {
            Log::error("Error enviando código OTP a {$user->email}: " . $e->getMessage());
            return back()->withErrors(['code' => 'No se pudo enviar el correo de verificación. Revisa los logs del servidor.']);
        }
    }
}