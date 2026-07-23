<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación Institucional - COTECNOVA</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; color: #333; margin: 0; padding: 20px; }
        .card { background-color: #ffffff; padding: 30px; border-radius: 10px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { text-align: center; border-bottom: 3px solid #10b981; padding-bottom: 15px; margin-bottom: 20px; }
        .header h2 { color: #0f172a; margin: 0; font-size: 22px; }
        .content { line-height: 1.6; color: #475569; font-size: 15px; }
        .btn-container { text-align: center; margin: 25px 0; }
        .btn { display: inline-block; background-color: #f17a28; color: #ffffff !important; padding: 12px 26px; text-decoration: none; border-radius: 6px; font-weight: bold; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h2>🏛️ COTECNOVA</h2>
        </div>

        <div class="content">
            @if(in_array($tipoAlerta ?? 'registro', ['registro', 'registro_admin']))
                <p>¡Hola, <strong>{{ $usuario->name ?? $usuario->nombre_estudiante ?? $usuario->username }}</strong>! 👋</p>
                
                <p><strong>¡Bienvenido(a)! Gracias por registrarte en nuestra plataforma institucional.</strong></p>
                
                <p>De antemano, te agradecemos que nos dejes conocerte un poco más. Te invitamos a ingresar al sistema y completar tu cuestionario para brindarte un mejor acompañamiento en tu proceso académico y de bienestar.</p>
                
                <div class="btn-container">
                    <a href="{{ route('login') }}" class="btn">Iniciar Sesión e Ingresar al Cuestionario</a>
                </div>
            @else
                <p>Hola, <strong>{{ $usuario->name ?? $usuario->nombre_estudiante ?? $usuario->username }}</strong>.</p>
                <p>Te informamos que la información de tu cuestionario o seguimiento académico ha sido actualizada correctamente en el sistema.</p>
            @endif
        </div>

        <div class="footer">
            <p>Este es un mensaje automático del Sistema de Información de COTECNOVA. Por favor no respondas a este correo.</p>
        </div>
    </div>
</body>
</html>