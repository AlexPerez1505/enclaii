<?php

// app/Jobs/UpdateWhatsappMessageStatus.php
namespace App\Jobs;

use App\Models\ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateWhatsappMessageStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $wamid;
    protected $status;

    public function __construct($wamid, $status)
    {
        $this->wamid = $wamid;
        $this->status = $status;
    }

    public function handle()
    {
        ChatMessage::where('wamid', $this->wamid)->update([
            'status' => $this->status
        ]);
    }
}
