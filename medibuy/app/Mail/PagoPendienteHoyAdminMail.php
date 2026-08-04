<?php

namespace App\Mail;

use App\Models\Venta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // ✅ Importa Log aquí

class PagoPendienteHoyAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $venta;

    public function __construct(Venta $venta)
    {
        $this->venta = $venta;
    }

    public function build()
    {
        Log::debug('Se está generando el correo para el admin desde PagoPendienteHoyAdminMail');

        return $this->subject('Alerta: Pago programado hoy para cliente')
                    ->view('emails.pago_pendiente_admin');
    }
}
