<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiChecklistService
{
    protected string $key;
    protected string $primaryModel;
    protected array $fallbackModels;
    protected int $timeout;

    public function __construct()
    {
        $this->key            = (string) config('services.openai.key', env('OPENAI_API_KEY'));
        $this->primaryModel   = (string) config('services.openai.primary', env('OPENAI_MODEL', env('AI_MODEL_PRIMARY', 'gpt-4o')));
        $this->fallbackModels = (array) config('services.openai.fallback_models', []);
        $this->timeout        = (int) config('services.openai.timeout', (int) env('AI_TIMEOUT', 45));
    }

    /**
     * Genera checklist estructurado para tickets.
     * Retorna un ARRAY de items con forma:
     * [
     *   ["text"=>"...", "required"=>true, "evidence_required"=>false, "evidence_types"=>["image","pdf"]],
     *   ...
     * ]
     */
    public function generateChecklist(string $title, string $description, string $ticketType, ?string $area = null): array
    {
        $models = array_values(array_filter(array_merge([$this->primaryModel], $this->fallbackModels)));
        if (empty($models)) {
            $models = ['gpt-4o'];
        }

        $payload = $this->buildPayload($title, $description, $ticketType, $area);

        $lastErr = null;
        foreach ($models as $model) {
            try {
                $payload['model'] = $model;

                $res = Http::withToken($this->key)
                    ->timeout($this->timeout)
                    ->acceptJson()
                    ->asJson()
                    ->post('https://api.openai.com/v1/responses', $payload);

                if (!$res->successful()) {
                    $msg = $res->json('error.message') ?: $res->body();
                    throw new \RuntimeException("OpenAI error ({$model}): {$res->status()} {$msg}");
                }

                $data = $res->json();

                // Extraer texto JSON del output (Responses API)
                $jsonText = $this->extractOutputText($data);

                $decoded = json_decode($jsonText, true);
                if (!is_array($decoded)) {
                    throw new \RuntimeException("La IA no devolvió JSON válido. Raw: " . Str::limit((string)$jsonText, 2000));
                }

                // Puede venir {"items":[...]} o directo [...]
                $items = $decoded['items'] ?? $decoded;

                if (!is_array($items)) {
                    throw new \RuntimeException("Formato inesperado: se esperaba array 'items' o array directo.");
                }

                // Normalizar forma
                $normalized = [];
                foreach ($items as $it) {
                    if (!is_array($it)) continue;

                    $normalized[] = [
                        'text'              => (string) ($it['text'] ?? ''),
                        'required'          => (bool) ($it['required'] ?? false),
                        'evidence_required' => (bool) ($it['evidence_required'] ?? false),
                        'evidence_types'    => array_values(array_filter((array) ($it['evidence_types'] ?? []))),
                    ];
                }

                // Quitar vacíos
                $normalized = array_values(array_filter($normalized, fn($x) => trim($x['text']) !== ''));

                return $normalized;
            } catch (\Throwable $e) {
                $lastErr = $e;
                // intenta siguiente modelo
            }
        }

        throw new \RuntimeException($lastErr?->getMessage() ?: 'OpenAI error desconocido.');
    }

    protected function buildPayload(string $title, string $description, string $ticketType, ?string $area): array
    {
        $areaTxt = $area ? "Área: {$area}" : "Área: (no especificada)";

        $instruction = <<<SYS
Eres un asistente experto en procesos operativos.
Debes generar un CHECKLIST para completar un ticket.
Devuelve SOLO JSON válido y nada más.

Reglas:
- Entre 5 y 15 items (según amerite).
- Cada item debe tener:
  - text (string)
  - required (boolean)
  - evidence_required (boolean)
  - evidence_types (array de strings) con valores permitidos: image, video, audio, pdf, spreadsheet, document, link, other
- Si evidence_required = true, agrega evidence_types (uno o más).
SYS;

        $user = <<<USR
Título: {$title}
Descripción: {$description}
Tipo: {$ticketType}
{$areaTxt}

Genera checklist.
USR;

        // JSON Schema (Structured Outputs) vía text.format
        return [
            'input' => [
                [
                    'role' => 'system',
                    'content' => [
                        ['type' => 'input_text', 'text' => $instruction],
                    ],
                ],
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'input_text', 'text' => $user],
                    ],
                ],
            ],

            // ✅ AQUÍ está el cambio clave: ya NO response_format, ahora text.format
            'text' => [
                'format' => [
                    'type' => 'json_schema',
                    'name' => 'ticket_checklist',
                    'strict' => true,
                    'schema' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'properties' => [
                            'items' => [
                                'type' => 'array',
                                'minItems' => 1,
                                'items' => [
                                    'type' => 'object',
                                    'additionalProperties' => false,
                                    'properties' => [
                                        'text' => ['type' => 'string'],
                                        'required' => ['type' => 'boolean'],
                                        'evidence_required' => ['type' => 'boolean'],
                                        'evidence_types' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'string',
                                                'enum' => ['image','video','audio','pdf','spreadsheet','document','link','other']
                                            ],
                                        ],
                                    ],
                                    'required' => ['text','required','evidence_required','evidence_types'],
                                ],
                            ],
                        ],
                        'required' => ['items'],
                    ],
                ],
            ],

            'temperature' => 0.2,
        ];
    }

    /**
     * Respuestas pueden venir en output[].content[].type=output_text
     */
    protected function extractOutputText(array $data): string
    {
        $out = $data['output'] ?? [];
        if (!is_array($out)) return '';

        foreach ($out as $msg) {
            $content = $msg['content'] ?? [];
            if (!is_array($content)) continue;

            foreach ($content as $c) {
                if (($c['type'] ?? null) === 'output_text' && isset($c['text'])) {
                    return (string) $c['text'];
                }
            }
        }

        // fallback “por si acaso”
        return (string) ($data['output_text'] ?? '');
    }
}