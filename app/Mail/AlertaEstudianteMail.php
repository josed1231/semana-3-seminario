<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlertaEstudianteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario; 
    public string $tipoAlerta;

    public function __construct($usuario, string $tipoAlerta = 'registro')
    {
        $this->usuario = $usuario;
        $this->tipoAlerta = $tipoAlerta; 
    }

    public function envelope(): Envelope
    {
        $asunto = match ($this->tipoAlerta) {
            'registro', 'registro_admin'     => '¡Bienvenido(a) al Aplicativo Institucional COTECNOVA!',
            'cuestionario', 'cuestionario_completado' => '¡Cuestionario Completado con Éxito! - COTECNOVA',
            default                          => 'Notificación Institucional - COTECNOVA',
        };

        return new Envelope(subject: $asunto);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.alerta-estudiante',
        );
    }
}