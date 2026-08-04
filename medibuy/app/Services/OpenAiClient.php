<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAiClient
{
    public function __construct(
        private readonly string $apiKey,
        private readonly int $timeoutSeconds = 45,
    ) {}

    /**
     * Llama Responses API. Devuelve texto final.
     * Docs: https://platform.openai.com/docs/api-reference/responses
     */
    public function responses(string $model, string $instructions, string $input): string
    {
        $resp = Http::timeout($this->timeoutSeconds)
            ->withToken($this->apiKey)
            ->acceptJson()
            ->post('https://api.openai.com/v1/responses', [
                'model' => $model,
                'instructions' => $instructions,
                'input' => $input,
            ]);

        if (!$resp->ok()) {
            throw new \RuntimeException("OpenAI HTTP {$resp->status()}: " . $resp->body());
        }

        $json = $resp->json();

        if (isset($json['output_text']) && is_string($json['output_text'])) {
            return $json['output_text'];
        }

        $text = '';
        foreach (($json['output'] ?? []) as $out) {
            foreach (($out['content'] ?? []) as $c) {
                if (($c['type'] ?? null) === 'output_text' && isset($c['text'])) {
                    $text .= $c['text'];
                }
            }
        }

        return trim($text);
    }
}
