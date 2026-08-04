<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class ExpoPushService
{
    public function send(array $tokens, string $title, string $body, array $data = [])
    {
        $messages = collect($tokens)->map(fn($token) => [
            'to' => $token,
            'sound' => 'default',
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'priority' => 'high',
        ])->values()->all();

        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('https://exp.host/--/api/v2/push/send', $messages);

        return $res->json();
    }
}