<?php

namespace App\Mail;

use App\Models\SolicitudMaterial;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SolicitudMaterialMarkdown extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;

    public function __construct(SolicitudMaterial $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva solicitud de material',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.solicitud_material',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
