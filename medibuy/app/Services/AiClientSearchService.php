<?php

namespace App\Services;

use OpenAI;
use Throwable;

class AiClientSearchService
{
    private string $apiKey;
    private string $primaryModel;
    private array $fallbackModels;
    private int $timeout;

    public function __construct(
        ?string $apiKey = null,
        ?string $primaryModel = null,
        ?array $fallbackModels = null,
        ?int $timeout = null
    ){
        $cfg = config('services.openai', []);
        $this->apiKey         = $apiKey         ?: ($cfg['key'] ?? env('OPENAI_API_KEY'));
        $this->primaryModel   = $primaryModel   ?: ($cfg['primary'] ?? env('AI_MODEL_PRIMARY', 'gpt-4o'));
        $this->fallbackModels = $fallbackModels ?: ($cfg['fallback_models'] ?? array_filter(array_map('trim', explode(',', env('OPENAI_FALLBACK_MODELS', 'gpt-4o,gpt-4o-mini,gpt-4.1-mini')))));
        $this->timeout        = (int)($timeout  ?: ($cfg['timeout'] ?? (int)env('AI_TIMEOUT', 45)));
    }

    /** Normaliza un texto libre en señales de búsqueda para la DB. */
    public function normalizeQuery(string $free): array
    {
        $models = array_values(array_unique(array_filter([$this->primaryModel, ...$this->fallbackModels])));
        $lastErr = null;

        foreach ($models as $m) {
            try {
                return $this->callModel($m, $free);
            } catch (Throwable $e) {
                $lastErr = $e;
                \Log::warning("AI cliente normalize: fallo con modelo {$m}: ".$e->getMessage());
                continue;
            }
        }

        // Fallback final sin IA
        \Log::warning('AI cliente normalize fallback sin IA: '.$lastErr?->getMessage());
        return [
            'nombre'=>null,'apellido'=>null,'email'=>null,'telefono'=>null,
            'palabras_clave'=>[$free],
        ];
    }

    private function callModel(string $model, string $free): array
    {
        $client = OpenAI::factory()
            ->withApiKey($this->apiKey)
            ->withHttpClient(new \GuzzleHttp\Client(['timeout'=>$this->timeout]))
            ->make();

        $system = <<<SYS
Eres un asistente que ESTRUCTURA consultas para buscar clientes en una base de datos.
Devuelve SOLO JSON válido con este shape (strings o null):

{
  "nombre": "string|null",
  "apellido": "string|null",
  "email": "string|null",
  "telefono": "string|null",
  "palabras_clave": ["..."]
}

- Extrae lo que puedas del texto del usuario.
- "palabras_clave" debe ser una lista corta (1-3 términos).
- No incluyas otros campos ni texto fuera del JSON.
SYS;

        $messages = [
            ['role'=>'system', 'content'=>$system],
            ['role'=>'user',   'content'=> json_encode(['q'=>$free], JSON_UNESCAPED_UNICODE)],
        ];

        $res = $client->chat()->create([
            'model'       => $model,
            'temperature' => 0.1,
            'messages'    => $messages,
            'max_tokens'  => 240,
        ]);

        $raw  = trim((string)($res->choices[0]->message->content ?? '{}'));
        $json = json_decode(preg_replace('/^```json\s*|\s*```$/u', '', $raw), true);

        if (!is_array($json)) throw new \RuntimeException('Respuesta IA inválida');
        $json += ['nombre'=>null,'apellido'=>null,'email'=>null,'telefono'=>null,'palabras_clave'=>[]];
        if (!is_array($json['palabras_clave'])) $json['palabras_clave'] = [];

        // Sanea strings
        foreach (['nombre','apellido','email','telefono'] as $k) {
            if (!is_null($json[$k])) $json[$k] = trim((string)$json[$k]);
        }
        $json['palabras_clave'] = array_values(array_filter(array_map(fn($x)=>trim((string)$x), $json['palabras_clave'])));

        return $json;
    }
}
