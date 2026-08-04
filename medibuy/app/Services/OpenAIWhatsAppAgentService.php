<?php

namespace App\Services;

use App\Models\ChatMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIWhatsAppAgentService
{
    /**
     * return:
     *  [
     *    'reply_text' => string,
     *    'handover' => bool,
     *    'handover_reason' => string,
     *    'handover_summary' => string,
     *    'action' => 'none' | 'create_maintenance_remision',
     *    'action_args' => array
     *  ]
     */
    public function decide(string $msisdn, string $latestUserText): ?array
    {
        $key = (string) config('services.openai.key', env('OPENAI_API_KEY', ''));
        if ($key === '') {
            Log::warning('OPENAI_MISSING_KEY');
            return null;
        }

        $primary   = (string) config('services.openai.primary', env('AI_MODEL_PRIMARY', 'gpt-4o'));
        $fallbacks = config('services.openai.fallback_models', []);
        if (is_string($fallbacks)) {
            $fallbacks = array_filter(array_map('trim', explode(',', $fallbacks)));
        }
        if (!is_array($fallbacks)) $fallbacks = [];

        $models = array_values(array_filter(array_unique(array_merge([$primary], $fallbacks))));

        $temp     = (float) env('OPENAI_TEMPERATURE', 0.4);
        $maxOut   = (int)   env('OPENAI_MAX_OUTPUT_TOKENS', 520);
        $store    = (bool)  env('OPENAI_STORE', false);
        $timeout  = (int)   config('services.openai.timeout', (int) env('AI_TIMEOUT', 45));

        $historyLimit = (int) env('WA_AI_HISTORY_LIMIT', 14);

        // Historial corto
        $history = ChatMessage::where(function ($q) use ($msisdn) {
                $q->where('from', $msisdn)->orWhere('to', $msisdn);
            })
            ->orderBy('wa_timestamp', 'desc')
            ->limit($historyLimit)
            ->get()
            ->reverse()
            ->values();

        $input = [];
        foreach ($history as $m) {
            $txt = trim((string) $m->text);
            if ($txt === '') continue;

            $input[] = [
                'role'    => $m->direction === 'in' ? 'user' : 'assistant',
                'content' => $this->clip($txt, 1200),
            ];
        }

        $latestUserText = trim($latestUserText);
        if ($latestUserText !== '') {
            $input[] = ['role' => 'user', 'content' => $this->clip($latestUserText, 1200)];
        }

        /** @var \App\Services\ProductCatalogTools $catalog */
        $catalog = app(ProductCatalogTools::class);

        // Tools de catálogo (function calling)
        $tools = [
            [
                'type' => 'function',
                'name' => 'search_products',
                'description' => 'Busca productos por texto/marca/tipo/modelo. Usa "" cuando no aplique.',
                'parameters' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'q'           => ['type' => 'string'],
                        'marca'       => ['type' => 'string'],
                        'tipo_equipo' => ['type' => 'string'],
                        'modelo'      => ['type' => 'string'],
                        'limit'       => ['type' => 'integer', 'minimum' => 1, 'maximum' => 10],
                    ],
                    'required' => ['q','marca','tipo_equipo','modelo','limit'],
                ],
                'strict' => true,
            ],
            [
                'type' => 'function',
                'name' => 'get_product_by_id',
                'description' => 'Obtiene 1 producto exacto por id.',
                'parameters' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'id' => ['type' => 'integer', 'minimum' => 1],
                    ],
                    'required' => ['id'],
                ],
                'strict' => true,
            ],
            [
                'type' => 'function',
                'name' => 'list_top_in_stock',
                'description' => 'Lista productos con buena disponibilidad (no mostrar stock numérico).',
                'parameters' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 12],
                    ],
                    'required' => ['limit'],
                ],
                'strict' => true,
            ],
        ];

        // ✅ SOLO habilita Web Search si detectamos intención técnica de equipo médico
        $enableWebSearch = $this->shouldEnableWebSearch($latestUserText);
        if ($enableWebSearch && env('WA_EQUIP_INFO_ENABLE_WEBSEARCH', true)) {
            $tools[] = $this->buildWebSearchTool();
        }

        $instructions = implode("\n", [
            "Eres el asistente de WhatsApp de MediBuy.",
            "Responde en español, claro, directo y útil (WhatsApp).",
            "",
            "CATÁLOGO:",
            "- NO inventes precios/modelos/marcas. SIEMPRE usa tools del catálogo (search_products/get_product_by_id) si te piden precio o disponibilidad.",
            "- Si hay varias coincidencias: máximo 5 opciones con: id, marca, tipo_equipo, modelo, precio y disponibilidad.",
            "- PROHIBIDO mostrar stock/cantidades. Nunca muestres números de stock.",
            "- Disponibilidad: usa solo '✅ Disponible' o '⛔ Agotado'.",
            "",
            "INFO TÉCNICA DE EQUIPO MÉDICO (cuando pregunten características/especificaciones/manual/compatibilidad):",
            "- Puedes responder con información técnica aunque NO esté en la BD.",
            "- Si está habilitado, usa web_search para no alucinar y basarte en fuentes.",
            "- Si no estás 100% seguro, dilo y pide el nombre exacto del equipo + marca + modelo.",
            "- NO des instrucciones médicas/diagnóstico clínico. Esto es solo información técnica del equipo.",
            "- Formato recomendado: Resumen, Especificaciones (bullets), Compatibilidad/uso, Recomendaciones, Preguntas para confirmar.",
            "",
            "HANDOVER:",
            "- handover=true si: el usuario lo pide, licitación/negociación compleja, queja fuerte, o falta info crítica.",
            "",
            "REMISIÓN MANTENIMIENTO:",
            "- Si el usuario pide remisión/orden de servicio/ticket/PDF:",
            "  - Junta lo mínimo primero.",
            "  - SOLO si ya tienes lo mínimo: action='create_maintenance_remision'.",
            "  - Mínimo: items (>=1), aplicar_iva (bool), tiene_envio (bool).",
            "  - Si tiene_envio=true y falta envio_costo o envio_direccion, pregunta antes (NO dispares action).",
            "  - items: cantidad, unidad, nombre_item, descripcion_item, importe_unitario.",
            "  - meta_pairs: lista de pares key/value (ej: equipo, marca, modelo, serie, falla...).",
            "",
            "FORMATO:",
            "- Devuelve SOLO JSON válido con el schema. Sin texto extra.",
        ]);

        $schema = $this->decisionSchema();

        Log::info('OPENAI_SCHEMA_VERSION', ['v' => '2026-01-09_websearch_equipo_v1']);

        foreach ($models as $model) {
            $decision = $this->runWithModel(
                model: $model,
                key: $key,
                baseInput: $input,
                tools: $tools,
                instructions: $instructions,
                schema: $schema,
                temp: $temp,
                maxOut: $maxOut,
                store: $store,
                timeout: $timeout,
                catalog: $catalog,
                includeWebSources: $enableWebSearch
            );

            if (is_array($decision)) {
                $decision = $this->normalizeDecision($decision);
                Log::info('OPENAI_OK', ['model' => $model, 'action' => $decision['action'] ?? 'none']);
                return $decision;
            }
        }

        return null;
    }

    /**
     * ✅ Schema estricto:
     * - SIN oneOf
     * - SIN nullables
     * - SIN meta
     * - Todos los objects con additionalProperties=false
     */
    private function decisionSchema(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'properties' => [
                'reply_text'        => ['type' => 'string'],
                'handover'          => ['type' => 'boolean'],
                'handover_reason'   => ['type' => 'string'],
                'handover_summary'  => ['type' => 'string'],

                'action' => [
                    'type' => 'string',
                    'enum' => ['none', 'create_maintenance_remision'],
                ],

                'action_args' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'items' => [
                            'type' => 'array',
                            'minItems' => 0,
                            'items' => [
                                'type' => 'object',
                                'additionalProperties' => false,
                                'properties' => [
                                    'cantidad' => ['type' => 'integer', 'minimum' => 1],
                                    'unidad' => ['type' => 'string'],
                                    'nombre_item' => ['type' => 'string'],
                                    'descripcion_item' => ['type' => 'string'],
                                    'importe_unitario' => ['type' => 'number', 'minimum' => 0],
                                ],
                                'required' => ['cantidad','unidad','nombre_item','descripcion_item','importe_unitario'],
                            ],
                        ],

                        'aplicar_iva' => ['type' => 'boolean'],
                        'tiene_envio' => ['type' => 'boolean'],

                        'envio_costo' => ['type' => 'number', 'minimum' => 0],
                        'envio_direccion' => ['type' => 'string'],
                        'meses_a_pagar' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 36],

                        'cliente_nombre' => ['type' => 'string'],
                        'cliente_apellido' => ['type' => 'string'],
                        'cliente_telefono' => ['type' => 'string'],
                        'cliente_email' => ['type' => 'string'],
                        'cliente_direccion' => ['type' => 'string'],

                        'meta_pairs' => [
                            'type' => 'array',
                            'minItems' => 0,
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
                    'required' => [
                        'items','aplicar_iva','tiene_envio',
                        'envio_costo','envio_direccion','meses_a_pagar',
                        'cliente_nombre','cliente_apellido','cliente_telefono','cliente_email','cliente_direccion',
                        'meta_pairs',
                    ],
                ],
            ],
            'required' => [
                'reply_text','handover','handover_reason','handover_summary',
                'action','action_args',
            ],
        ];
    }

    private function normalizeDecision(array $decision): array
    {
        $decision['reply_text']       = $this->clip((string)($decision['reply_text'] ?? ''), 1700);
        $decision['handover_reason']  = $this->clip((string)($decision['handover_reason'] ?? ''), 300);
        $decision['handover_summary'] = $this->clip((string)($decision['handover_summary'] ?? ''), 900);
        $decision['handover']         = (bool)($decision['handover'] ?? false);

        $action = (string)($decision['action'] ?? 'none');
        if (!in_array($action, ['none','create_maintenance_remision'], true)) $action = 'none';
        $decision['action'] = $action;

        $args = is_array($decision['action_args'] ?? null) ? $decision['action_args'] : [];
        $decision['action_args'] = $this->withDefaultActionArgs($args);

        if ($action === 'none') {
            $decision['action_args']['items'] = [];
            $decision['action_args']['aplicar_iva'] = false;
            $decision['action_args']['tiene_envio'] = false;
            $decision['action_args']['envio_costo'] = 0;
            $decision['action_args']['envio_direccion'] = '';
            $decision['action_args']['meses_a_pagar'] = 0;
            $decision['action_args']['meta_pairs'] = [];
        }

        return $decision;
    }

    private function withDefaultActionArgs(array $a): array
    {
        if (!isset($a['items']) || !is_array($a['items'])) $a['items'] = [];
        $a['aplicar_iva'] = (bool)($a['aplicar_iva'] ?? false);
        $a['tiene_envio'] = (bool)($a['tiene_envio'] ?? false);

        $a['envio_costo'] = (float)($a['envio_costo'] ?? 0);
        if ($a['envio_costo'] < 0) $a['envio_costo'] = 0;

        $a['envio_direccion'] = (string)($a['envio_direccion'] ?? '');
        $a['meses_a_pagar'] = (int)($a['meses_a_pagar'] ?? 0);
        if ($a['meses_a_pagar'] < 0) $a['meses_a_pagar'] = 0;
        if ($a['meses_a_pagar'] > 36) $a['meses_a_pagar'] = 36;

        $a['cliente_nombre'] = (string)($a['cliente_nombre'] ?? '');
        $a['cliente_apellido'] = (string)($a['cliente_apellido'] ?? '');
        $a['cliente_telefono'] = (string)($a['cliente_telefono'] ?? '');
        $a['cliente_email'] = (string)($a['cliente_email'] ?? '');
        $a['cliente_direccion'] = (string)($a['cliente_direccion'] ?? '');

        $a['meta_pairs'] = is_array($a['meta_pairs'] ?? null) ? $a['meta_pairs'] : [];

        // Normaliza items
        $normItems = [];
        foreach ($a['items'] as $it) {
            if (!is_array($it)) continue;

            $cantidad = (int)($it['cantidad'] ?? 1);
            if ($cantidad < 1) $cantidad = 1;

            $unidad = (string)($it['unidad'] ?? '');
            $nombre = (string)($it['nombre_item'] ?? '');
            $desc   = (string)($it['descripcion_item'] ?? '');
            $precio = (float)($it['importe_unitario'] ?? 0);
            if ($precio < 0) $precio = 0;

            if (trim($nombre) === '') continue;

            $normItems[] = [
                'cantidad' => $cantidad,
                'unidad' => $unidad,
                'nombre_item' => $this->clip($nombre, 180),
                'descripcion_item' => $this->clip($desc, 500),
                'importe_unitario' => $precio,
            ];
        }
        $a['items'] = $normItems;

        // Normaliza meta_pairs
        $pairs = [];
        foreach ($a['meta_pairs'] as $p) {
            if (!is_array($p)) continue;
            $k = trim((string)($p['key'] ?? ''));
            $v = trim((string)($p['value'] ?? ''));
            if ($k === '' || $v === '') continue;
            $pairs[] = ['key' => $this->clip($k, 60), 'value' => $this->clip($v, 220)];
        }
        $a['meta_pairs'] = $pairs;

        return $a;
    }

    private function runWithModel(
        string $model,
        string $key,
        array $baseInput,
        array $tools,
        string $instructions,
        array $schema,
        float $temp,
        int $maxOut,
        bool $store,
        int $timeout,
        ProductCatalogTools $catalog,
        bool $includeWebSources = false
    ): ?array {
        $inputList = $baseInput;

        for ($i = 0; $i < 3; $i++) {
            $payload = [
                'model' => $model,
                'instructions' => $instructions,
                'input' => $inputList,
                'tools' => $tools,
                'temperature' => $temp,
                'max_output_tokens' => $maxOut,
                'store' => $store,
                'text' => [
                    'format' => [
                        'type' => 'json_schema',
                        'name' => 'wa_decision',
                        'strict' => true,
                        'schema' => $schema,
                    ],
                ],
            ];

            // ✅ Si se habilitó web_search, pedimos sources para poder mandarlas al usuario
            // (OpenAI soporta include web_search_call.action.sources) :contentReference[oaicite:3]{index=3}
            if ($includeWebSources) {
                $payload['tool_choice'] = 'auto';
                $payload['include'] = ['web_search_call.action.sources'];
            }

            $resp = Http::withToken($key)
                ->acceptJson()
                ->asJson()
                ->timeout($timeout)
                ->post('https://api.openai.com/v1/responses', $payload);

            if (!$resp->successful()) {
                $body = $resp->json();
                Log::warning('OPENAI_FAIL', [
                    'model'  => $model,
                    'status' => $resp->status(),
                    'body'   => $body,
                ]);

                if ($this->isModelAccessProblem($resp->status(), $body)) {
                    return null;
                }
                return null;
            }

            $j = $resp->json() ?: [];
            $output = (array)($j['output'] ?? []);

            // reenviar output como parte del siguiente input
            foreach ($output as $item) {
                $inputList[] = $item;
            }

            // ✅ Captura fuentes si hubo web_search_call
            $webSources = $this->extractWebSourcesFromOutput($output);

            $didTool = false;

            foreach ($output as $item) {
                if (($item['type'] ?? '') !== 'function_call') continue;

                $didTool = true;

                $name   = (string)($item['name'] ?? '');
                $callId = (string)($item['call_id'] ?? '');

                $args = json_decode((string)($item['arguments'] ?? '{}'), true);
                if (!is_array($args)) $args = [];

                foreach (['q','marca','tipo_equipo','modelo'] as $k) {
                    if (isset($args[$k]) && is_string($args[$k])) $args[$k] = trim($args[$k]);
                    if (!isset($args[$k]) || !is_string($args[$k])) $args[$k] = '';
                }
                if (!isset($args['limit']) || !is_int($args['limit'])) $args['limit'] = 5;

                try {
                    if ($name === 'search_products') {
                        $toolOut = $catalog->searchProducts($args);
                    } elseif ($name === 'get_product_by_id') {
                        $toolOut = $catalog->getProductById($args);
                    } elseif ($name === 'list_top_in_stock') {
                        $toolOut = $catalog->listTopInStock($args);
                    } else {
                        $toolOut = ['error' => 'unknown_tool', 'name' => $name];
                    }
                } catch (\Throwable $e) {
                    Log::error('CATALOG_TOOL_ERR', [
                        'tool' => $name,
                        'ex'   => $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                    ]);
                    $toolOut = ['error' => 'tool_exception', 'message' => $e->getMessage()];
                }

                // NO stock numérico
                $toolOut = $this->sanitizeCatalogOutput($toolOut);

                $inputList[] = [
                    'type' => 'function_call_output',
                    'call_id' => $callId,
                    'output' => json_encode($toolOut, JSON_UNESCAPED_UNICODE),
                ];
            }

            if ($didTool) continue;

            $outText = trim((string)($j['output_text'] ?? ''));
            if ($outText === '') {
                $outText = trim((string)$this->extractTextFromOutput($output));
            }

            if ($outText === '') {
                Log::warning('OPENAI_EMPTY_OUTPUT_TEXT', ['model' => $model, 'resp' => $j]);
                return null;
            }

            $decision = json_decode($outText, true);
            if (!is_array($decision)) {
                Log::warning('OPENAI_BAD_DECISION', ['model' => $model, 'text' => $outText]);
                return null;
            }

            // ✅ Si hay fuentes web, anexarlas al reply_text (máx 3 URLs)
            if (!empty($webSources)) {
                $decision['reply_text'] = $this->appendSourcesToReply(
                    (string)($decision['reply_text'] ?? ''),
                    $webSources
                );
            }

            return $decision;
        }

        Log::warning('OPENAI_TOOL_LOOP_EXHAUSTED', ['model' => $model]);
        return null;
    }

    /**
     * Tool: web_search con allowlist de dominios (configurable).
     * OpenAI permite filters.allowed_domains en web_search. :contentReference[oaicite:4]{index=4}
     */
    private function buildWebSearchTool(): array
    {
        $raw = trim((string) env('WA_EQUIP_INFO_ALLOWED_DOMAINS', ''));
        $domains = [];

        if ($raw !== '') {
            $domains = array_values(array_filter(array_map(function ($d) {
                $d = trim($d);
                $d = preg_replace('#^https?://#', '', $d);
                $d = rtrim($d, '/');
                return $d;
            }, explode(',', $raw))));
        }

        // fallback razonable (puedes ajustar a tus marcas)
        if (empty($domains)) {
            $domains = [
                'fujifilm.com',
                'fujifilmhealthcare.com',
                'olympus-global.com',
                'pentaxmedical.com',
                'stryker.com',
                'karlstorz.com',
                'www.fda.gov',
                'accessdata.fda.gov',
            ];
        }

        return [
            'type' => 'web_search',
            'filters' => [
                'allowed_domains' => array_slice($domains, 0, 100),
            ],
        ];
    }

    /**
     * Detecta intención de “info técnica” (especificaciones, ficha técnica, manual, compatibilidad, etc.)
     * para habilitar web_search sólo cuando conviene (menos costo/ruido).
     */
    private function shouldEnableWebSearch(string $text): bool
    {
        $t = mb_strtolower(trim($text));
        if ($t === '') return false;

        // keywords
        if (preg_match('/\b(caracter[ií]sticas|especificaciones|specs|ficha t[eé]cnica|manual|datasheet|compatibilidad|compatible|puertos|conexiones|resoluci[oó]n|sensor|procesador|c[aá]mara|endoscop|colonoscop|gastroscop|laparoscopy|torre|fuente de luz|light source)\b/u', $t)) {
            return true;
        }

        // patrón “modelo” + números (1588, 1688, eg-530, etc.)
        if (preg_match('/\b(modelo|model)\b.*\b([a-z]{1,3}[- ]?\d{2,5}|\d{3,5})\b/u', $t)) {
            return true;
        }

        // “qué es / para qué sirve”
        if (preg_match('/\b(que es|qu[eé] es|para qu[eé] sirve|funci[oó]n|uso|aplicaci[oó]n)\b/u', $t) && preg_match('/\b(\d{3,5}|eg[- ]?\d{2,5})\b/u', $t)) {
            return true;
        }

        return false;
    }

    /**
     * Quita stock numérico y deja solo disponibilidad.
     */
    private function sanitizeCatalogOutput($data)
    {
        if (is_array($data)) {
            if (array_key_exists('stock', $data)) {
                $stockVal = $data['stock'];
                $num = is_numeric($stockVal) ? (float)$stockVal : null;
                $data['disponibilidad'] = ($num !== null && $num > 0) ? '✅ Disponible' : '⛔ Agotado';
                unset($data['stock']);
            }

            foreach ($data as $k => $v) {
                $data[$k] = $this->sanitizeCatalogOutput($v);
            }
            return $data;
        }

        return $data;
    }

    private function isModelAccessProblem(int $status, $body): bool
    {
        $code = (string) data_get($body, 'error.code', '');
        $msg  = (string) data_get($body, 'error.message', '');

        if (in_array($status, [401, 403, 404], true)) {
            if (str_contains($msg, 'does not have access to model')) return true;
            if ($code === 'model_not_found') return true;
            if (str_contains($msg, 'model') && str_contains($msg, 'not found')) return true;
        }

        return false;
    }

    private function extractTextFromOutput(array $output): string
    {
        foreach ($output as $item) {
            if (($item['type'] ?? '') !== 'message') continue;

            $content = $item['content'] ?? null;

            if (is_string($content)) return $content;

            if (is_array($content)) {
                foreach ($content as $c) {
                    if (($c['type'] ?? '') === 'output_text' && isset($c['text'])) return (string) $c['text'];
                    if (($c['type'] ?? '') === 'text' && isset($c['text'])) return (string) $c['text'];
                }
            }
        }
        return '';
    }

    /**
     * Extrae URLs de sources devueltas por web_search_call.action.sources
     * (vía include). :contentReference[oaicite:5]{index=5}
     */
    private function extractWebSourcesFromOutput(array $output): array
    {
        $urls = [];

        foreach ($output as $item) {
            if (($item['type'] ?? '') !== 'web_search_call') continue;

            $sources = data_get($item, 'action.sources', []);
            if (!is_array($sources)) continue;

            foreach ($sources as $s) {
                $url = (string) data_get($s, 'url', '');
                if ($url !== '') $urls[] = $url;
            }
        }

        $urls = array_values(array_unique(array_filter($urls)));
        return $urls;
    }

    private function appendSourcesToReply(string $reply, array $urls): string
    {
        $reply = trim($reply);
        if (empty($urls)) return $reply;

        $top = array_slice($urls, 0, 3);

        // WhatsApp clickable:
        $block = "\n\nFuentes:\n";
        $i = 1;
        foreach ($top as $u) {
            $block .= ($i++) . ") " . $u . "\n";
        }

        // evita duplicar si ya trae "Fuentes:"
        if (mb_stripos($reply, 'fuentes:') !== false) return $reply;

        return $this->clip($reply . rtrim($block), 1700);
    }

    private function clip(string $s, int $max): string
    {
        $s = trim($s);
        if ($s === '') return '';
        return mb_strlen($s) > $max ? (mb_substr($s, 0, $max - 1) . '…') : $s;
    }
}
