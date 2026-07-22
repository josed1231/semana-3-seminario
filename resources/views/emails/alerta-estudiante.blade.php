<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notificación Institucional</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e1e1e1; border-radius: 5px;">
        <h2 style="color: #0284c7;">Cotecnova - Sistema de Detección Temprana</h2>
        
        <p>Estimado/a <strong>{{ $usuario->nombre_estudiante ?? $usuario->name }}</strong>,</p>

        {{-- Lógica según el rol o tipo de alerta --}}
        @if($tipoAlerta === 'registro_admin' || (isset($usuario->rol) && $usuario->rol !== 'user'))
            <p>¡Te han registrado exitosamente dentro del aplicativo institucional!</p>
            <p>Ya dispones de acceso al sistema con tu rol asignado para la gestión y seguimiento.</p>
        @else
            @if($tipoAlerta === 'bienvenida')
                <p>¡Bienvenido/a! Te has registrado correctamente dentro del cuestionario semestral.</p>
            @else
                <p>Gracias por llenar el cuestionario semestral. Apreciamos mucho tu tiempo y <strong>tendremos muy en cuenta tus respuestas</strong>.</p>
            @endif
        @endif

        <div style="background-color: #f8fafc; padding: 15px; border-radius: 4px; margin: 15px 0;">
            <p style="margin: 0 0 10px 0;"><strong>Correo registrado:</strong> {{ $usuario->correo ?? $usuario->email }}</p>
            @if(isset($usuario->codigo_estudiante))
                <p style="margin: 0 0 10px 0;"><strong>Código:</strong> {{ $usuario->codigo_estudiante }}</p>
            @endif
        </div>

        <p>Le invitamos a ingresar al portal académico si requiere soporte o contacto institucional.</p>

        <hr style="border: none; border-top: 1px solid #e1e1e1; margin: 20px 0;">
        <p style="font-size: 12px; color: #64748b; text-align: center;">Este es un mensaje automático generado por el módulo de predicción y alertas.</p>
    </div>
</body>
</html>