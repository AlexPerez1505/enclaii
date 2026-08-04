<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected $twilio;
    protected $from;

    public function __construct()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $this->from = env('TWILIO_WHATSAPP_FROM');
        $this->twilio = new Client($sid, $token);
    }

    public function enviarWhatsapp($to, $message)
    {
        try {
            $toFormatted = 'whatsapp:+' . $to;

            $response = $this->twilio->messages->create(
                $toFormatted,
                [
                    'from' => $this->from,
                    'body' => $message
                ]
            );

            Log::info("Mensaje enviado a $toFormatted con SID: " . $response->sid);
            return $response->sid;
        } catch (\Exception $e) {
            Log::error("Error al enviar mensaje: " . $e->getMessage());
            return null;
        }
    }
}
