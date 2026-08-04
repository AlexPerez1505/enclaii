<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\WhatsAppService;

class SendWhatsappInteractive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $to;
    protected $text;
    protected $buttons;

    /**
     * @param string $to  Número destino en formato E164 (ej: 5215512345678)
     * @param string $text Texto principal
     * @param array $buttons Ej: [['id' => 'cotizar', 'title' => 'Cotizar']]
     */
    public function __construct(string $to, string $text, array $buttons)
    {
        $this->to      = $to;
        $this->text    = $text;
        $this->buttons = $buttons;
    }

    public function handle()
    {
        WhatsAppService::sendInteractiveButtons($this->to, $this->text, $this->buttons);
    }
}
