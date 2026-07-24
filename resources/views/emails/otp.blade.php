<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de verificación - COTECNOVA</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; color: #333; margin: 0; padding: 20px; }
        .card { background-color: #ffffff; padding: 30px; border-radius: 10px; max-width: 500px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; }
        .header h2 { color: #0f172a; margin-bottom: 5px; }
        .otp-code { font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #f17a28; background: #fff3eb; padding: 15px; border-radius: 8px; margin: 20px 0; display: inline-block; }
        .footer { font-size: 12px; color: #94a3b8; margin-top: 25px; border-top: 1px solid #e2e8f0; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h2>🏛️ COTECNOVA</h2>
            <p>Verificación de Seguridad (2FA)</p>
        </div>

        <p>Hola, <strong>{{ $user->name ?? $user->username }}</strong> 👋</p>
        <p>Tu código de verificación para iniciar sesión es:</p>

        <div class="otp-code">{{ $otp }}</div>

        <p><small>Este código es válido por <strong>5 minutos</strong>. Si no solicitaste este código, ignora este mensaje.</small></p>

        <div class="footer">
            <p>Este es un mensaje automático del Sistema de Información de COTECNOVA.</p>
        </div>
    </div>
</body>
</html>