<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RemisionSlotExtractorService
{
    public function extract(string $text): array
    {
        $text = trim($text);
        if ($text === '') return [];

        $key = (string) config('services.openai.key', env('OPENAI_API_KEY', ''));
        if ($key === '') return [];

        $model   = (string) config('services.openai.primary', env('AI_MODEL_PRIMARY', 'gpt-4o-mini'));
        $timeout = (int)   config('services.openai.timeout', (int) env('AI_TIMEOUT', 45));

        // Schema ESTRICTO (sin oneOf, sin nullables, sin additionalProperties:true)
        $schema = [
            'type' => 'object',
            'additionalProperties' => false,
            'properties' => [
                'cliente_nombre'   => ['type' => 'string'],
                'cliente_apellido' => ['type' => 'string'],
                'cliente_telefono' => ['type' => 'string'],
                'cliente_email'    => ['type' => 'string'],
                'cliente_direccion'=> ['type' => 'string'],

                'aplicar_iva' => ['type' => 'string', 'enum' => ['','si','no']],   // '' = no detectado
                'tiene_envio' => ['type' => 'string', 'enum' => ['','si','no']],   // '' = no detectado
                'envio_costo' => ['type' => 'number'],
                'envio_direccion' => ['type' => 'string'],

                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'properties' => [
                            'cantidad' => ['type' => 'integer'],
                            'unidad' => ['type' => 'string'],
                            'nombre_item' => ['type' => 'string'],
                            'descripcion_item' => ['type' => 'string'],
                            'importe_unitario' => ['type' => 'number'],
                        ],
                        'required' => ['cantidad','unidad','nombre_item','descripcion_item','importe_unitario'],
                    ],
                ],

                'meta_pairs' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'properties' => [
                            'key' => ['type' => 'string'],
                            'value' => ['type' => 'string'],
                        ],
                        'required' => ['key','value'],
                    ],
                ],
            ],
            // IMPORTANT: exigimos que siempre regrese estas llaves (aunque vengan vacías)
            'required' => [
                'cliente_nombre','cliente_apellido','cliente_telefono','cliente_email','cliente_direccion',
                'aplicar_iva','tiene_envio','envio_costo','envio_direccion',
                'items','meta_pairs'
            ],
        ];

        $instructions = implode("\n", [
            "Extrae datos para una remisión de mantenimiento desde el mensaje del usuario.",
            "Reglas:",
            "- Si NO detectas un campo, devuélvelo vacío: string '' o items/meta_pairs vacíos.",
            "- aplicar_iva y tiene_envio deben ser: 'si', 'no' o ''.",
            "- items: si el usuario describe servicio + precio, crea 1 item con cantidad=1 y unidad='servicio' si no se especifica.",
            "- meta_pairs: detecta 'equipo', 'marca', 'modelo', 'serie', 'falla' si aparecen.",
            "- Devuelve SOLO JSON válido.",
        ]);

        try {
            $resp = Http::withToken($key)
                ->acceptJson()
                ->asJson()
                ->timeout($timeout)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => $model,
                    'instructions' => $instructions,
                    'input' => [
                        ['role' => 'user', 'content' => $text],
                    ],
                    'max_output_tokens' => 300,
                    'temperature' => 0.1,
                    'text' => [
                        'format' => [
                            'type' => 'json_schema',
                            'name' => 'remision_extract',
                            'strict' => true,
                            'schema' => $schema,
                        ],
                    ],
                ]);

            if (!$resp->successful()) {
                Log::warning('REMISION_EXTRACT_FAIL', ['status'=>$resp->status(), 'body'=>$resp->json()]);
                return [];
            }

            $j = $resp->json() ?: [];
            $outText = trim((string)($j['output_text'] ?? ''));
            if ($outText === '') return [];

            $data = json_decode($outText, true);
            return is_array($data) ? $data : [];
        } catch (\Throwable $e) {
            Log::warning('REMISION_EXTRACT_EX', ['e'=>$e->getMessage()]);
            return [];
        }
    }
}
