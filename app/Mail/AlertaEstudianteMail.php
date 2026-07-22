<?php

namespace App\Mail;

use App\Models\Estudiante; // O tu modelo de usuario general
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlertaEstudianteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario; // Puede ser estudiante o usuario administrativo
    public string $tipoAlerta;

    public function __construct($usuario, string $tipoAlerta)
    {
        $this->usuario = $usuario;
        $this->tipoAlerta = $tipoAlerta; 
    }

    public function envelope(): Envelope
    {
        $asunto = $this->tipoAlerta === 'registro_admin' 
            ? '¡Registro Exitoso en el Aplicativo Institucional!' 
            : 'Actualización de Cuestionario Semestral - COTECNOVA';

        return new Envelope(subject: $asunto);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.alerta-estudiante',
        );
    }
}