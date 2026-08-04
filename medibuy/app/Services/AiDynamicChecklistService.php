<?php

namespace App\Services;

use Illuminate\Support\Str;
use OpenAI;
use OpenAI\Exceptions\ErrorException;
use Throwable;

class AiDynamicChecklistService
{
    private string $apiKey;
    private string $primaryModel;
    /** @var string[] */
    private array $fallbackModels;
    private int $timeout;
    private int $maxTokens;
    private float $temperature;
    private float $topP;

    private const RESULT_CATALOG = [
        'Bueno y Funcional',
        'Revisado',
        'Ajustado',
        'Reparado',
        'Reemplazado',
        'Realizado',
        'No aplica',
    ];

    private const MAX_SECTIONS = 5;
    private const MAX_ITEMS_PER_SECTION = 8;
    private const MAX_PARTIDAS = 6;

    public function __construct(
        ?string $apiKey = null,
        ?string $primaryModel = null,
        ?array  $fallbackModels = null,
        ?int    $timeout = null,
        ?int    $maxTokens = null,
        ?float  $temperature = null,
        ?float  $topP = null,
    ) {
        $cfg                  = config('services.openai', []);
        $this->apiKey         = $apiKey ?: ($cfg['key'] ?? env('OPENAI_API_KEY', ''));
        $this->primaryModel   = $primaryModel ?: ($cfg['primary'] ?? env('AI_MODEL_PRIMARY', 'gpt-4o'));
        $this->fallbackModels = $fallbackModels
            ?? ($cfg['fallback_models'] ?? array_filter(array_map('trim', explode(',', (string) env('OPENAI_FALLBACK_MODELS', 'gpt-4o-mini,gpt-4.1-mini')))));
        $this->timeout        = $timeout ?: (int)($cfg['timeout'] ?? (int) env('AI_TIMEOUT', 45));
        $this->maxTokens      = $maxTokens ?: 4000;
        $this->temperature    = $temperature ?: 0.05;
        $this->topP           = $topP ?: 1.0;
    }

    public function generateChecklist(string $freeText, array $context = [], array $opts = []): array
    {
        $meta = [
            'model_attempts' => [],
            'model_used'     => null,
            'latency_ms'     => null,
            'estado_ai'      => 'fallback_heuristico',
            'error'          => null,
        ];

        if (trim($this->apiKey) === '') {
            $meta['error'] = 'OPENAI_API_KEY no está configurada.';
            \Log::error('AI checklist: OPENAI_API_KEY vacía o no configurada.');

            $fallback = $this->postClamp($this->heuristicChecklist($freeText, $context));
            $fallback['_meta'] = $meta;

            return $this->maybeWithMeta($fallback, $meta, $opts);
        }

        $modelsToTry = array_values(array_unique(array_filter([
            $this->primaryModel,
            ...$this->fallbackModels
        ])));

        $lastError = null;

        foreach ($modelsToTry as $model) {
            $start = microtime(true);

            try {
                $meta['model_attempts'][] = $model;
                $result = $this->callModelWithRetries($model, $freeText, $context);

                $meta['model_used'] = $model;
                $meta['latency_ms'] = (int) round((microtime(true) - $start) * 1000);
                $meta['estado_ai']  = 'respuesta_modelo';

                $result = $this->postClamp($result);

                return $this->maybeWithMeta($result, $meta, $opts);
            } catch (Throwable $e) {
                $lastError = $e;
                $meta['error'] = $e->getMessage();

                \Log::warning('AI checklist: fallo de modelo', [
                    'model'   => $model,
                    'error'   => $e->getMessage(),
                    'class'   => get_class($e),
                    'timeout' => $this->timeout,
                ]);
            }
        }

        \Log::error('AI checklist: todos los modelos fallaron; usando heurística.', [
            'last_error' => $lastError?->getMessage(),
            'attempts'   => $meta['model_attempts'],
        ]);

        $fallback = $this->postClamp($this->heuristicChecklist($freeText, $context));

        return $this->maybeWithMeta($fallback, $meta, $opts);
    }

    private function callModelWithRetries(string $model, string $freeText, array $context): array
    {
        $ex = null;
        $delays = [0.8, 1.6, 2.8];

        for ($i = 0; $i < 3; $i++) {
            try {
                return $this->callModel($model, $freeText, $context);
            } catch (Throwable $e) {
                $ex = $e;

                if ($i < 2) {
                    usleep((int)(($delays[$i] + mt_rand(0, 300) / 1000) * 1_000_000));
                }
            }
        }

        throw $ex ?? new ErrorException("Fallo desconocido llamando [$model]");
    }

    private function callModel(string $model, string $freeText, array $context): array
    {
        $client = OpenAI::factory()
            ->withApiKey($this->apiKey)
            ->withHttpClient(new \GuzzleHttp\Client([
                'timeout' => $this->timeout,
            ]))
            ->make();

        $locale   = $context['locale'] ?? 'es';
        $servicio = $context['servicio'] ?? 'preventivo';
        $sintomas = $context['sintomas'] ?? null;

        $equipo        = trim((string)($context['equipo'] ?? ''));
        $marca         = trim((string)($context['marca'] ?? ''));
        $modelo        = trim((string)($context['modelo'] ?? ''));
        $numSerie      = trim((string)($context['numero_serie'] ?? ''));
        $observaciones = trim((string)($context['observaciones'] ?? ''));
        $taxonomia     = $context['taxonomia'] ?? null;

        $producto = is_array($context['producto'] ?? null) ? $context['producto'] : null;
        $productoTexto = '';
        if ($producto) {
            $productoTexto = trim(implode(' ', array_map(static fn($v) => (string)$v, $producto)));
        }

        $richName = trim(implode(' ', array_filter([$equipo, $marca, $modelo])));
        if ($richName === '') {
            $richName = $freeText;
        }

        $inputForFilter = trim(implode(' ', array_filter([
            $richName,
            $equipo,
            $marca,
            $modelo,
            $numSerie,
            $observaciones,
            $sintomas ?? '',
            $productoTexto,
        ])));

        $system = <<<SYS
Eres un INGENIERO BIOMÉDICO senior especialista en mantenimiento, diagnóstico, seguridad, remisiones técnicas y liberación de EQUIPO MÉDICO.
Tu salida debe ser TÉCNICA, CLARA, PROFESIONAL, NO repetitiva y útil para trabajo real en campo/taller/hospital.

Trabajas con: endoscopia (torres, cámaras, procesadores, fuentes de luz, insufladores, bombas), anestesia, ventilación, monitoreo,
hospitalización, esterilización, imagen, laboratorio, odontología, aspiración, electrocirugía y equipo médico general.

MUY IMPORTANTE:
- Si la entrada es ambigua, interprétala como equipo médico genérico.
- NO inventes puertos, funciones, botones, accesorios, protocolos o módulos que no aparezcan en:
  1) el texto del usuario
  2) producto_context
  3) producto_texto
- Si no estás seguro, usa descripciones generales.
- Las partidas de remisión deben corresponder a lo que realmente se hizo o se revisó.
- Las partidas deben ser EDITABLES después por el usuario, así que deben venir claras y prácticas.
- No pongas precios inventados altos o arbitrarios: si no tienes contexto comercial suficiente, usa 0.

Devuelve EXCLUSIVAMENTE un JSON válido en {$locale} con esta estructura:

{
  "equipo": {
    "nombre_detectado": "string",
    "categoria_inferida": "string",
    "marca_modelo": "string",
    "numero_serie": "string|null"
  },
  "secciones": [
    {
      "titulo": "string",
      "items": [
        {
          "nombre": "string",
          "resultado_sugerido": "Bueno y Funcional|Revisado|Ajustado|Reparado|Reemplazado|Realizado|No aplica"
        }
      ]
    }
  ],
  "acciones_sugeridas": ["string"],
  "riesgos_seguridad": ["string"],
  "diagnostico": {
    "hallazgos_probables": ["string"],
    "hipotesis": "string",
    "causa_raiz_probable": "string",
    "pruebas_sugeridas": ["string"],
    "pruebas_detalladas": [
      {
        "nombre": "string",
        "objetivo": "string",
        "procedimiento": ["string"],
        "resultado_esperado": "string"
      }
    ],
    "pasos_a_seguir": ["string"],
    "prioridad": "alta|media|baja",
    "nivel_riesgo": "alto|medio|bajo",
    "piezas_posibles": ["string"],
    "criterio_liberacion": ["string"],
    "recomendacion_final": "string"
  },
  "resumen_ingenieria": ["string"],
  "remision_partidas": [
    {
      "item": "Partida 1",
      "descripcion": "string",
      "unidad": "SERVICIO|PZA|KIT|LOTE",
      "cantidad": 1,
      "precio_unitario": 0
    }
  ],
  "notas": "string|null"
}

REGLAS:
- 3 a 5 secciones
- 4 a 8 ítems por sección
- Sin duplicados
- Ajusta el diagnóstico a los síntomas
- Si no hay síntomas, usa hipótesis preventivas y prioridad baja
- "pasos_a_seguir" debe venir como secuencia clara y profesional
- "pruebas_detalladas" debe servir para que un técnico sepa qué hacer
- "criterio_liberacion" debe indicar cómo decidir si el equipo puede liberarse
- Genera 2 a 5 partidas de remisión
- Las partidas deben reflejar lo realizado: diagnóstico, mantenimiento, limpieza, ajuste, reparación, pruebas, validación, reemplazo, etc.
- Si hay correctivo, incluye partida diagnóstica y/o correctiva
- Si hay preventivo, prioriza limpieza, inspección, ajuste, pruebas y validación
- precio_unitario debe ser numérico
- SOLO JSON válido, sin explicación extra
SYS;

        $fewUser = [
            'equipo_texto' => 'Fuente de luz endoscópica xenón',
            'servicio' => 'preventivo',
            'sintomas' => null,
            'detalle' => 'Ejemplo de formato',
            'marca' => null,
            'modelo' => null,
            'numero_serie' => null,
            'observaciones' => null,
        ];

        $fewAssistant = [
            "equipo" => [
                "nombre_detectado" => "Fuente de luz endoscópica",
                "categoria_inferida" => "fuente de luz endoscópica",
                "marca_modelo" => "Genérico",
                "numero_serie" => null
            ],
            "secciones" => [
                [
                    "titulo" => "Óptica y salida de luz",
                    "items" => [
                        ["nombre" => "Acople a cable de fibra y seguro", "resultado_sugerido" => "Revisado"],
                        ["nombre" => "Ventana/salida óptica (limpieza y daños)", "resultado_sugerido" => "Revisado"],
                        ["nombre" => "Uniformidad/intensidad de luz", "resultado_sugerido" => "Revisado"],
                        ["nombre" => "Control de intensidad (según modelo)", "resultado_sugerido" => "Realizado"]
                    ]
                ],
                [
                    "titulo" => "Lámpara y térmico",
                    "items" => [
                        ["nombre" => "Horas/contador de lámpara", "resultado_sugerido" => "Revisado"],
                        ["nombre" => "Encendido y estabilidad de luz", "resultado_sugerido" => "Revisado"],
                        ["nombre" => "Ventiladores y flujo de aire", "resultado_sugerido" => "Revisado"],
                        ["nombre" => "Prueba básica de seguridad eléctrica", "resultado_sugerido" => "Realizado"]
                    ]
                ]
            ],
            "acciones_sugeridas" => [
                "Limpieza de salida óptica y rejillas",
                "Verificar horas de lámpara y estado",
                "Prueba funcional de intensidad"
            ],
            "riesgos_seguridad" => [
                "No mirar directamente la salida de luz",
                "Desenergizar antes de abrir carcasa",
                "Permitir enfriamiento antes de manipular lámpara"
            ],
            "diagnostico" => [
                "hallazgos_probables" => [
                    "Acumulación de polvo en rejillas o zonas de ventilación",
                    "Desgaste natural de lámpara por horas de uso"
                ],
                "hipotesis" => "Sin síntomas reportados; el mantenimiento debe enfocarse en la estabilidad lumínica, ventilación y limpieza óptica.",
                "causa_raiz_probable" => "Desgaste por uso continuo y mantenimiento preventivo pendiente.",
                "pruebas_sugeridas" => [
                    "Prueba de intensidad y estabilidad de luz",
                    "Verificación de ventilación y alarmas térmicas"
                ],
                "pruebas_detalladas" => [
                    [
                        "nombre" => "Prueba de intensidad de luz",
                        "objetivo" => "Confirmar que la salida lumínica sea estable.",
                        "procedimiento" => [
                            "Encender el equipo según procedimiento seguro.",
                            "Ajustar intensidad en diferentes niveles.",
                            "Observar estabilidad, variación o parpadeo."
                        ],
                        "resultado_esperado" => "La luz debe mantenerse estable sin caída ni parpadeo anormal."
                    ]
                ],
                "pasos_a_seguir" => [
                    "Realizar inspección visual general.",
                    "Limpiar salida óptica y rejillas.",
                    "Verificar horas de lámpara.",
                    "Ejecutar prueba funcional.",
                    "Documentar hallazgos y liberar si cumple."
                ],
                "prioridad" => "baja",
                "nivel_riesgo" => "bajo",
                "piezas_posibles" => [],
                "criterio_liberacion" => [
                    "Encendido estable",
                    "Sin alarmas activas",
                    "Salida de luz uniforme",
                    "Sin daño visible en carcasa o acoples"
                ],
                "recomendacion_final" => "Equipo apto para continuar en operación si pasa pruebas funcionales y de seguridad."
            ],
            "resumen_ingenieria" => [
                "Inspección visual y limpieza óptica",
                "Revisión de ventilación y filtros",
                "Verificar contador y estado de lámpara",
                "Pruebas funcionales de intensidad/estabilidad",
                "Registro técnico y liberación"
            ],
            "remision_partidas" => [
                [
                    "item" => "Partida 1",
                    "descripcion" => "Mantenimiento preventivo general de fuente de luz endoscópica",
                    "unidad" => "SERVICIO",
                    "cantidad" => 1,
                    "precio_unitario" => 0
                ],
                [
                    "item" => "Partida 2",
                    "descripcion" => "Limpieza óptica, revisión de ventilación y prueba funcional",
                    "unidad" => "SERVICIO",
                    "cantidad" => 1,
                    "precio_unitario" => 0
                ],
                [
                    "item" => "Partida 3",
                    "descripcion" => "Validación operativa y liberación técnica",
                    "unidad" => "SERVICIO",
                    "cantidad" => 1,
                    "precio_unitario" => 0
                ]
            ],
            "notas" => null
        ];

        $userPayload = [
            'equipo_texto'           => $richName,
            'servicio'               => $servicio,
            'sintomas'               => $sintomas ?: null,
            'observaciones'          => $observaciones ?: null,
            'marca'                  => $marca ?: null,
            'modelo'                 => $modelo ?: null,
            'numero_serie'           => $numSerie ?: null,
            'detalle'                => 'Checklist técnico biomédico con diagnóstico detallado, pasos a seguir, criterio de liberación y partidas de remisión; responde solo JSON.',
            '_dominio'               => 'equipo_medico',
            '_taxonomia'             => $taxonomia,
            'producto_context'       => $producto,
            'producto_texto'         => $productoTexto,
            'producto_texto_detalle' => $productoTexto,
            '_input_concat'          => $inputForFilter,
        ];

        $messages = [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => json_encode($fewUser, JSON_UNESCAPED_UNICODE)],
            ['role' => 'assistant', 'content' => json_encode($fewAssistant, JSON_UNESCAPED_UNICODE)],
            ['role' => 'user', 'content' => json_encode($userPayload, JSON_UNESCAPED_UNICODE)],
        ];

        $params = [
            'model'       => $model,
            'temperature' => $this->temperature,
            'top_p'       => $this->topP,
            'max_tokens'  => $this->maxTokens,
            'messages'    => $messages,
        ];

        $res = $client->chat()->create($params);

        $raw  = (string) ($res->choices[0]->message->content ?? '');
        $json = $this->decodeStrictJson($raw);

        if (!$json || !isset($json['secciones'])) {
            $messages[] = [
                'role' => 'system',
                'content' => 'SOLO JSON válido. Sin texto extra. Sin duplicados. No inventes funciones. Incluye remision_partidas.'
            ];

            $res2 = $client->chat()->create($params + ['messages' => $messages]);
            $raw  = (string) ($res2->choices[0]->message->content ?? '');
            $json = $this->decodeStrictJson($raw);
        }

        if (!$json || !isset($json['secciones'])) {
            throw new ErrorException("Respuesta no válida del modelo [$model]");
        }

        return $this->sanitize($json, $inputForFilter, $servicio);
    }

    private function decodeStrictJson(string $raw): ?array
    {
        $clean = trim($raw);
        $clean = preg_replace('/^```json\s*|\s*```$/u', '', $clean);
        $clean = preg_replace('/^```\s*|\s*```$/u', '', $clean);
        $clean = trim($clean);

        $clean = preg_replace('/,\s*([\}\]])/m', '$1', $clean);

        if (strpos($clean, '"') === false && strpos($clean, "'") !== false) {
            $clean = str_replace("'", '"', $clean);
        }

        $data = json_decode($clean, true);
        if (is_array($data)) {
            return $data;
        }

        $first = strpos($clean, '{');
        $last  = strrpos($clean, '}');

        if ($first !== false && $last !== false && $last > $first) {
            $slice = substr($clean, $first, $last - $first + 1);
            $slice = preg_replace('/,\s*([\}\]])/m', '$1', $slice);

            $data2 = json_decode($slice, true);
            if (is_array($data2)) {
                return $data2;
            }
        }

        return null;
    }

    private function heuristicChecklist(string $freeText, array $context): array
    {
        $equipo   = trim((string)($context['equipo'] ?? ''));
        $marca    = trim((string)($context['marca'] ?? ''));
        $modelo   = trim((string)($context['modelo'] ?? ''));
        $serie    = trim((string)($context['numero_serie'] ?? ''));
        $sintomas = trim((string)($context['sintomas'] ?? ''));
        $obs      = trim((string)($context['observaciones'] ?? ''));
        $servicio = trim((string)($context['servicio'] ?? 'preventivo'));

        $producto = is_array($context['producto'] ?? null) ? $context['producto'] : null;
        $productoTexto = '';
        if ($producto) {
            $productoTexto = trim(implode(' ', array_map(static fn($v) => (string)$v, $producto)));
        }

        $richName = trim(implode(' ', array_filter([$equipo, $marca, $modelo]))) ?: $freeText;
        if ($productoTexto !== '') {
            $richName .= ' ' . $productoTexto;
        }

        $cat = $this->heuristicCategory($richName);
        $base = $this->heuristicSectionsByCategory($cat);
        $diag = $this->buildHeuristicDiagnosis($cat, $sintomas);

        $acciones = $this->defaultActionsByCategory($cat);
        $partidas = $this->buildHeuristicPartidas($cat, $servicio, $equipo, $marca, $modelo, $acciones);

        return $this->sanitize([
            'equipo' => [
                'nombre_detectado'   => $richName ?: 'equipo médico',
                'categoria_inferida' => $cat,
                'marca_modelo'       => trim(implode(' ', array_filter([$marca, $modelo]))) ?: $richName,
                'numero_serie'       => $serie !== '' ? $serie : null,
            ],
            'secciones' => $base,
            'acciones_sugeridas' => $acciones,
            'riesgos_seguridad' => $this->defaultRisksByCategory($cat),
            'diagnostico' => $diag,
            'resumen_ingenieria' => [
                'Inspección visual y limpieza',
                'Revisión de componentes críticos',
                'Pruebas funcionales guiadas por síntomas',
                'Verificación básica de seguridad eléctrica',
                'Documentación técnica y liberación'
            ],
            'remision_partidas' => $partidas,
            'notas' => $obs !== '' ? $obs : null,
        ], $richName . ' ' . $productoTexto, $servicio);
    }

    private function heuristicSectionsByCategory(string $cat): array
    {
        return match ($cat) {
            'fuente de luz endoscópica' => [
                [
                    'titulo' => 'Óptica y salida de luz',
                    'items' => [
                        ['nombre' => 'Acople a cable de fibra y seguro', 'resultado_sugerido' => 'Revisado'],
                        ['nombre' => 'Ventana/salida óptica (limpieza y daños)', 'resultado_sugerido' => 'Revisado'],
                        ['nombre' => 'Uniformidad/intensidad de luz', 'resultado_sugerido' => 'Revisado'],
                        ['nombre' => 'Control de intensidad (según modelo)', 'resultado_sugerido' => 'Realizado'],
                    ],
                ],
                [
                    'titulo' => 'Lámpara y enfriamiento',
                    'items' => [
                        ['nombre' => 'Horas/contador de lámpara', 'resultado_sugerido' => 'Revisado'],
                        ['nombre' => 'Encendido y estabilidad de luz', 'resultado_sugerido' => 'Revisado'],
                        ['nombre' => 'Ventiladores y flujo de aire', 'resultado_sugerido' => 'Revisado'],
                        ['nombre' => 'Rejillas/filtros de aire (limpieza)', 'resultado_sugerido' => 'Realizado'],
                    ],
                ],
                [
                    'titulo' => 'Eléctrico y seguridad',
                    'items' => [
                        ['nombre' => 'Cable de alimentación, fusibles y switch', 'resultado_sugerido' => 'Revisado'],
                        ['nombre' => 'Tierra física y continuidad', 'resultado_sugerido' => 'Realizado'],
                        ['nombre' => 'Prueba básica de seguridad eléctrica', 'resultado_sugerido' => 'Realizado'],
                        ['nombre' => 'Interlocks o tapas de seguridad', 'resultado_sugerido' => 'Revisado'],
                    ],
                ],
            ],

            'videoprocesador/cámara endoscópica' => [
                [
                    'titulo' => 'Cámara y óptica',
                    'items' => [
                        ['nombre' => 'Integridad del cabezal de cámara y cable', 'resultado_sugerido' => 'Revisado'],
                        ['nombre' => 'Limpieza de lente/frontal de cámara', 'resultado_sugerido' => 'Realizado'],
                        ['nombre' => 'Balance de blancos y exposición', 'resultado_sugerido' => 'Realizado'],
                        ['nombre' => 'Respuesta de botones en mango (si aplica)', 'resultado_sugerido' => 'Revisado'],
                    ],
                ],
                [
                    'titulo' => 'Procesamiento de imagen',
                    'items' => [
                        ['nombre' => 'Encendido y auto-test (si aplica)', 'resultado_sugerido' => 'Realizado'],
                        ['nombre' => 'Procesamiento de imagen (color, nitidez, ruido)', 'resultado_sugerido' => 'Revisado'],
                        ['nombre' => 'Prueba de salida de video hacia monitor según modelo', 'resultado_sugerido' => 'Realizado'],
                    ],
                ],
                [
                    'titulo' => 'Seguridad y montaje',
                    'items' => [
                        ['nombre' => 'Fijación en torre y ventilación adecuada', 'resultado_sugerido' => 'Revisado'],
                        ['nombre' => 'Cable de alimentación y tierra física', 'resultado_sugerido' => 'Revisado'],
                        ['nombre' => 'Etiquetado/identificación del sistema', 'resultado_sugerido' => 'Revisado'],
                    ],
                ],
            ],

            'insuflador' => [
                [
                    'titulo' => 'Conexiones y suministro',
                    'items' => [
                        ['nombre' => 'Entrada de CO₂', 'resultado_sugerido' => 'Revisado'],
                        ['nombre' => 'Líneas y filtros', 'resultado_sugerido' => 'Revisado'],
                        ['nombre' => 'Conexiones generales', 'resultado_sugerido' => 'Realizado'],
                    ],
                ],
                [
                    'titulo' => 'Parámetros y seguridad',
                    'items' => [
                        ['nombre' => 'Ajuste de presión', 'resultado_sugerido' => 'Realizado'],
                        ['nombre' => 'Ajuste de flujo', 'resultado_sugerido' => 'Realizado'],
                        ['nombre' => 'Alarmas', 'resultado_sugerido' => 'Revisado'],
                    ],
                ],
                [
                    'titulo' => 'Integridad',
                    'items' => [
                        ['nombre' => 'Prueba de fugas', 'resultado_sugerido' => 'Realizado'],
                        ['nombre' => 'Integridad de chasis', 'resultado_sugerido' => 'Bueno y Funcional'],
                    ],
                ],
            ],

            'máquina de anestesia' => [
                [
                    'titulo' => 'Gases y circuito',
                    'items' => [
                        ['nombre' => 'Conexión de gases y reguladores', 'resultado_sugerido' => 'Revisado'],
                        ['nombre' => 'Prueba de fugas del circuito', 'resultado_sugerido' => 'Realizado'],
                        ['nombre' => 'Absorbedor de CO₂ y sellos', 'resultado_sugerido' => 'Revisado'],
                    ],
                ],
                [
                    'titulo' => 'Controles y seguridad',
                    'items' => [
                        ['nombre' => 'Flujómetros', 'resultado_sugerido' => 'Revisado'],
                        ['nombre' => 'Válvula APL', 'resultado_sugerido' => 'Revisado'],
                        ['nombre' => 'O₂ flush', 'resultado_sugerido' => 'Realizado'],
                        ['nombre' => 'Alarmas y auto-test', 'resultado_sugerido' => 'Realizado'],
                    ],
                ],
            ],

            default => [
                [
                    'titulo' => 'Inspección general',
                    'items' => [
                        ['nombre' => 'Integridad de chasis', 'resultado_sugerido' => 'Revisado'],
                        ['nombre' => 'Conexiones eléctricas', 'resultado_sugerido' => 'Revisado'],
                        ['nombre' => 'Limpieza externa', 'resultado_sugerido' => 'Realizado'],
                        ['nombre' => 'Etiquetado/identificación', 'resultado_sugerido' => 'Revisado'],
                    ],
                ],
            ],
        };
    }

    private function defaultActionsByCategory(string $cat): array
    {
        $c = mb_strtolower($cat);

        return match (true) {
            str_contains($c, 'fuente de luz') => [
                'Realizar limpieza de salida óptica y rejillas',
                'Verificar horas de uso y condición de lámpara',
                'Ejecutar prueba funcional de intensidad',
                'Documentar resultados y criterio de liberación'
            ],
            str_contains($c, 'videoprocesador') || str_contains($c, 'cámara endosc') => [
                'Limpiar cabezal y superficie óptica',
                'Verificar calidad de imagen hacia monitor',
                'Revisar estabilidad operativa del procesador',
                'Documentar resultado final'
            ],
            str_contains($c, 'insuflador') => [
                'Verificar líneas y filtros',
                'Probar presión, flujo y alarmas',
                'Confirmar integridad general',
                'Registrar hallazgos y liberación'
            ],
            str_contains($c, 'anest') => [
                'Ejecutar auto-test',
                'Realizar prueba de fugas',
                'Verificar alarmas y controles',
                'Documentar aptitud operativa'
            ],
            default => [
                'Realizar inspección visual general',
                'Ejecutar prueba funcional básica',
                'Verificar seguridad eléctrica básica',
                'Registrar condición final del equipo'
            ],
        };
    }

    private function defaultRisksByCategory(string $cat): array
    {
        $c = mb_strtolower($cat);

        return match (true) {
            str_contains($c, 'fuente de luz') => [
                'No mirar directamente la salida de luz',
                'Desenergizar antes de abrir el equipo',
                'Esperar enfriamiento antes de manipular componentes térmicos'
            ],
            default => [
                'Desconectar de la red eléctrica antes de abrir el equipo',
                'Usar EPP según procedimiento interno',
                'No liberar el equipo sin pruebas mínimas de funcionamiento'
            ],
        };
    }

    private function buildHeuristicDiagnosis(string $cat, string $sintomas): array
    {
        $s = Str::lower($sintomas);

        $prioridad = 'baja';
        $riesgo = 'bajo';
        $probables = [];
        $pruebas = [];
        $pruebasDetalladas = [];
        $pasos = [];
        $piezas = [];
        $causaRaiz = 'No determinada aún; requiere validación técnica.';
        $recomendacionFinal = 'Equipo sujeto a revisión técnica antes de liberación.';
        $criterioLiberacion = [
            'Sin daño físico relevante',
            'Sin alarmas persistentes',
            'Pruebas funcionales satisfactorias',
            'Condición segura para operación'
        ];

        if ($s === '') {
            $probables[] = 'Sin síntomas reportados';
            $pruebas[] = 'Ejecutar protocolo preventivo estándar';
            $causaRaiz = 'Mantenimiento preventivo o revisión de rutina.';
            $recomendacionFinal = 'Realizar mantenimiento preventivo completo y liberar solo si pasa todas las pruebas.';
        }

        if (Str::contains($s, ['no enciende', 'sin encender', 'no power'])) {
            $prioridad = 'alta';
            $riesgo = 'alto';
            $probables[] = 'Falla de alimentación principal';
            $pruebas[] = 'Verificar entrada AC, fusible, switch y fuente';
            $pasos[] = 'Confirmar presencia de alimentación eléctrica';
            $pasos[] = 'Inspeccionar fusible y cable de alimentación';
            $pasos[] = 'Verificar fuente o módulo de potencia';
            $piezas[] = 'Fusible';
            $piezas[] = 'Cable de alimentación';
            $piezas[] = 'Fuente de poder';
            $causaRaiz = 'Probable falla en alimentación, protección o fuente interna.';
            $recomendacionFinal = 'No liberar el equipo hasta recuperar encendido estable y completar prueba funcional.';
            $pruebasDetalladas[] = [
                'nombre' => 'Prueba de alimentación',
                'objetivo' => 'Confirmar si el equipo recibe y procesa correctamente la energía de entrada.',
                'procedimiento' => [
                    'Verificar cable y conexión eléctrica.',
                    'Confirmar presencia de voltaje en entrada.',
                    'Revisar fusibles y switch de encendido.',
                    'Validar funcionamiento de la fuente interna.'
                ],
                'resultado_esperado' => 'El equipo debe energizar correctamente sin caída ni protección anormal.'
            ];
        }

        if ($cat === 'fuente de luz endoscópica') {
            if (Str::contains($s, ['no da luz', 'sin luz', 'luz tenue', 'baja intensidad', 'parpadea'])) {
                $prioridad = 'alta';
                $riesgo = 'medio';
                $probables[] = 'Lámpara degradada o inestabilidad del módulo de potencia';
                $pruebas[] = 'Verificar horas de lámpara';
                $pruebas[] = 'Comprobar estabilidad de intensidad de luz';
                $pruebas[] = 'Revisar limpieza de salida óptica';
                $pasos[] = 'Inspeccionar condición de lámpara';
                $pasos[] = 'Limpiar salida óptica';
                $pasos[] = 'Probar intensidad en distintos niveles';
                $pasos[] = 'Documentar estabilidad o parpadeo';
                $piezas[] = 'Lámpara';
                $piezas[] = 'Ballast';
                $piezas[] = 'Módulo de potencia';
                $causaRaiz = 'Probable desgaste de lámpara o inestabilidad en etapa de potencia/encendido.';
                $recomendacionFinal = 'Liberar solo si la intensidad es uniforme, estable y segura para procedimiento clínico.';
                $pruebasDetalladas[] = [
                    'nombre' => 'Prueba de intensidad luminosa',
                    'objetivo' => 'Confirmar estabilidad y calidad de la salida de luz.',
                    'procedimiento' => [
                        'Encender el equipo de forma segura.',
                        'Ajustar intensidad en niveles bajo, medio y alto.',
                        'Observar si hay caída de intensidad, parpadeo o apagado.',
                        'Validar temperatura de operación y respuesta estable.'
                    ],
                    'resultado_esperado' => 'La luz debe ser uniforme, estable y sin interrupciones.'
                ];
                $criterioLiberacion = [
                    'Encendido estable',
                    'Salida lumínica uniforme',
                    'Sin parpadeo o apagado inesperado',
                    'Sin alarmas térmicas o eléctricas'
                ];
            }

            if (Str::contains($s, ['sobrecalienta', 'se apaga', 'ventilador', 'alarma termica', 'temperatura'])) {
                $prioridad = 'alta';
                $riesgo = 'alto';
                $probables[] = 'Problema de ventilación o sensor térmico';
                $pruebas[] = 'Inspeccionar ventiladores y flujo de aire';
                $pruebas[] = 'Validar estado de rejillas/filtros';
                $pruebas[] = 'Verificar sensores térmicos';
                $pasos[] = 'Revisar ventiladores';
                $pasos[] = 'Limpiar rejillas y filtros';
                $pasos[] = 'Comprobar alarmas térmicas';
                $pasos[] = 'Ejecutar prueba de operación continua';
                $piezas[] = 'Ventilador';
                $piezas[] = 'Sensor térmico';
                $piezas[] = 'Filtro';
                $causaRaiz = 'Disipación térmica deficiente o falla de detección térmica.';
                $recomendacionFinal = 'No liberar el equipo mientras persista riesgo de sobretemperatura.';
                $pruebasDetalladas[] = [
                    'nombre' => 'Prueba térmica de operación',
                    'objetivo' => 'Confirmar que el equipo mantenga temperatura segura durante funcionamiento.',
                    'procedimiento' => [
                        'Encender el equipo.',
                        'Observar ventiladores y flujo de aire.',
                        'Mantener operación durante un periodo controlado.',
                        'Verificar si se activan alarmas o apagado térmico.'
                    ],
                    'resultado_esperado' => 'El equipo debe operar sin sobrecalentamiento ni alarmas térmicas.'
                ];
                $criterioLiberacion = [
                    'Ventilación funcional',
                    'Sin sobretemperatura',
                    'Sin apagado térmico',
                    'Operación estable durante prueba continua'
                ];
            }
        }

        if ($cat === 'insuflador' && Str::contains($s, ['no insufla', 'presión', 'flujo', 'alarma'])) {
            $prioridad = 'alta';
            $riesgo = 'alto';
            $probables[] = 'Falla de sensor, restricción u obstrucción en línea';
            $pruebas[] = 'Prueba de fuga y verificación de sensores';
            $pasos[] = 'Revisar líneas y filtros';
            $pasos[] = 'Verificar presión y flujo';
            $pasos[] = 'Comprobar respuesta de alarmas';
            $piezas[] = 'Sensor';
            $piezas[] = 'Solenoide';
            $piezas[] = 'Filtro';
            $causaRaiz = 'Posible obstrucción, fuga o lectura incorrecta de sensores.';
            $recomendacionFinal = 'No liberar si no mantiene presión/flujo dentro de parámetros esperados.';
        }

        if ($cat === 'máquina de anestesia' && Str::contains($s, ['fuga', 'alarm', 'presión'])) {
            $prioridad = 'alta';
            $riesgo = 'alto';
            $probables[] = 'Fuga en circuito o defecto en válvula APL';
            $pruebas[] = 'Prueba de fugas del circuito';
            $pasos[] = 'Inspeccionar sellos y conexiones';
            $pasos[] = 'Realizar prueba de fugas';
            $pasos[] = 'Validar función de APL y alarmas';
            $piezas[] = 'Válvula APL';
            $piezas[] = 'Empaques';
            $causaRaiz = 'Probable pérdida de integridad en circuito respiratorio o control de presión.';
            $recomendacionFinal = 'No liberar el equipo hasta confirmar circuito hermético y alarmas operativas.';
        }

        if (empty($pasos)) {
            $pasos = [
                'Realizar inspección visual general',
                'Verificar integridad física y conexiones',
                'Ejecutar pruebas funcionales básicas',
                'Registrar hallazgos técnicos',
                'Definir liberación o envío a reparación'
            ];
        }

        return [
            'hallazgos_probables' => $this->arrayUniqueCI($probables),
            'hipotesis' => $s !== ''
                ? 'Diagnóstico preliminar basado en síntomas reportados y comportamiento esperado del equipo.'
                : 'Revisión preventiva sin síntomas específicos reportados.',
            'causa_raiz_probable' => $causaRaiz,
            'pruebas_sugeridas' => $this->arrayUniqueCI($pruebas),
            'pruebas_detalladas' => $pruebasDetalladas,
            'pasos_a_seguir' => $this->arrayUniqueCI($pasos),
            'prioridad' => $prioridad,
            'nivel_riesgo' => $riesgo,
            'piezas_posibles' => $s === '' ? [] : $this->arrayUniqueCI($piezas),
            'criterio_liberacion' => $this->arrayUniqueCI($criterioLiberacion),
            'recomendacion_final' => $recomendacionFinal,
        ];
    }

    private function buildHeuristicPartidas(string $cat, string $servicio, string $equipo, string $marca, string $modelo, array $acciones): array
    {
        $servicio = mb_strtolower(trim($servicio));
        $nombreEquipo = trim(implode(' ', array_filter([$equipo, $marca, $modelo])));
        if ($nombreEquipo === '') {
            $nombreEquipo = $cat !== '' ? $cat : 'equipo médico';
        }

        $accionesTxt = implode(', ', array_slice($acciones, 0, 4));

        if ($servicio === 'correctivo') {
            return [
                [
                    'item' => 'Partida 1',
                    'descripcion' => 'Diagnóstico técnico y revisión correctiva de ' . $nombreEquipo,
                    'unidad' => 'SERVICIO',
                    'cantidad' => 1,
                    'precio_unitario' => 0,
                ],
                [
                    'item' => 'Partida 2',
                    'descripcion' => $accionesTxt !== ''
                        ? 'Corrección, ajuste o reparación realizada: ' . $accionesTxt
                        : 'Corrección, ajuste o reparación de falla detectada',
                    'unidad' => 'SERVICIO',
                    'cantidad' => 1,
                    'precio_unitario' => 0,
                ],
                [
                    'item' => 'Partida 3',
                    'descripcion' => 'Pruebas funcionales, validación operativa y liberación técnica',
                    'unidad' => 'SERVICIO',
                    'cantidad' => 1,
                    'precio_unitario' => 0,
                ],
            ];
        }

        if ($servicio === 'mixto') {
            return [
                [
                    'item' => 'Partida 1',
                    'descripcion' => 'Mantenimiento preventivo / correctivo de ' . $nombreEquipo,
                    'unidad' => 'SERVICIO',
                    'cantidad' => 1,
                    'precio_unitario' => 0,
                ],
                [
                    'item' => 'Partida 2',
                    'descripcion' => $accionesTxt !== ''
                        ? 'Actividades realizadas durante el servicio: ' . $accionesTxt
                        : 'Limpieza, diagnóstico, ajuste y corrección según condición encontrada',
                    'unidad' => 'SERVICIO',
                    'cantidad' => 1,
                    'precio_unitario' => 0,
                ],
                [
                    'item' => 'Partida 3',
                    'descripcion' => 'Pruebas funcionales, validación final y entrega técnica',
                    'unidad' => 'SERVICIO',
                    'cantidad' => 1,
                    'precio_unitario' => 0,
                ],
            ];
        }

        return [
            [
                'item' => 'Partida 1',
                'descripcion' => 'Mantenimiento preventivo general de ' . $nombreEquipo,
                'unidad' => 'SERVICIO',
                'cantidad' => 1,
                'precio_unitario' => 0,
            ],
            [
                'item' => 'Partida 2',
                'descripcion' => $accionesTxt !== ''
                    ? 'Limpieza, inspección y ajuste realizados: ' . $accionesTxt
                    : 'Limpieza, inspección, ajuste y revisión general',
                'unidad' => 'SERVICIO',
                'cantidad' => 1,
                'precio_unitario' => 0,
            ],
            [
                'item' => 'Partida 3',
                'descripcion' => 'Pruebas funcionales, validación final y liberación técnica',
                'unidad' => 'SERVICIO',
                'cantidad' => 1,
                'precio_unitario' => 0,
            ],
        ];
    }

    private function heuristicCategory(string $t): string
    {
        $t = Str::lower(Str::ascii($t));

        return match (true) {
            Str::contains($t, ['fuente de luz', 'light source', 'xenon', 'xenón', 'led light', 'clv', 'olympus clv', 'storz', 'stryker light', 'circon', 'wolf']) => 'fuente de luz endoscópica',
            Str::contains($t, ['videoprocesador', 'video procesador', 'procesador de video', 'camera endoscopica', 'camara endoscopica', 'ccu']) => 'videoprocesador/cámara endoscópica',
            Str::contains($t, ['insufl']) => 'insuflador',
            Str::contains($t, ['anest']) => 'máquina de anestesia',
            Str::contains($t, ['ventil']) => 'ventilador',
            Str::contains($t, ['monitor signos', 'monitor paciente', 'ecg', 'spo2', 'nibp', 'etco2', 'ibp']) => 'monitor de paciente',
            Str::contains($t, ['gastro', 'gastroscop']) => 'gastroscopio',
            Str::contains($t, ['colono', 'colonoscop']) => 'colonoscopio',
            Str::contains($t, ['bronco', 'broncoscop']) => 'broncoscopio',
            Str::contains($t, ['arco en c', 'c-arm', 'fluorosc']) => 'arco en C',
            Str::contains($t, ['rayos x', 'rx', 'radiograf']) => 'rayos X',
            Str::contains($t, ['ultraso', 'ecogra', 'sonogra']) => 'ultrasonido',
            Str::contains($t, ['autoclave', 'esteriliz']) => 'autoclave',
            Str::contains($t, ['lavadora', 'termodesinfect']) => 'lavadora termodesinfectadora',
            Str::contains($t, ['infusion', 'infusión', 'jeringa', 'syringe pump']) => 'bomba de infusión/jeringa',
            Str::contains($t, ['desfibr', 'defibr']) => 'desfibrilador',
            Str::contains($t, ['taladro', 'drill', 'shaver', 'microdebr']) => 'power tool ortopédico',
            Str::contains($t, ['cama', 'camilla', 'mesa de cir']) => 'mobiliario hospitalario',
            default => 'equipo médico',
        };
    }

    private function portTokens(): array
    {
        return [
            'usb', 'hdmi', 'dvi', 'sdi', 'displayport', 'dp', 'vga',
            'lan', 'ethernet', 'rj45', 'wifi', 'wi fi', 'wi-fi',
            'rs-232', 'rs232', 'serial', 'com',
            'hl7', 'dicom', 'pacs'
        ];
    }

    private function filterPortItems(array $sections, string $inputText): array
    {
        $tokens = $this->portTokens();
        $inputNorm = $this->normalizeLite($inputText);
        $allowedTokens = [];

        foreach ($tokens as $tk) {
            if (str_contains($inputNorm, $tk)) {
                $allowedTokens[] = $tk;
            }
        }

        $out = [];

        foreach ($sections as $sec) {
            $itemsOut = [];

            foreach (($sec['items'] ?? []) as $it) {
                $name = (string)($it['nombre'] ?? '');
                $norm = $this->normalizeLite($name);

                $foundTokens = [];
                foreach ($tokens as $tk) {
                    if (str_contains($norm, $tk)) {
                        $foundTokens[] = $tk;
                    }
                }

                if ($foundTokens) {
                    $isAllowed = false;
                    foreach ($foundTokens as $tk) {
                        if (in_array($tk, $allowedTokens, true)) {
                            $isAllowed = true;
                            break;
                        }
                    }

                    if (!$isAllowed) {
                        continue;
                    }
                }

                $itemsOut[] = $it;
            }

            if (!empty($itemsOut)) {
                $sec['items'] = $itemsOut;
                $out[] = $sec;
            }
        }

        return $out;
    }

    private function sanitize(array $json, string $inputText = '', string $servicio = 'preventivo'): array
    {
        $json['equipo'] = $json['equipo'] ?? [];
        $json['equipo'] += [
            'nombre_detectado'   => (string) ($json['equipo']['nombre_detectado'] ?? ''),
            'categoria_inferida' => (string) ($json['equipo']['categoria_inferida'] ?? ''),
            'marca_modelo'       => (string) ($json['equipo']['marca_modelo'] ?? ''),
            'numero_serie'       => isset($json['equipo']['numero_serie']) && $json['equipo']['numero_serie'] !== ''
                ? (string) $json['equipo']['numero_serie']
                : null,
        ];

        $secsOut = [];
        $seenSec = [];
        $synSec  = $this->sectionCanonicalSynonyms();

        foreach (($json['secciones'] ?? []) as $sec) {
            $titulo = trim((string)($sec['titulo'] ?? 'Sección'));
            if ($titulo === '') {
                continue;
            }

            $canon = $synSec[mb_strtolower($titulo)] ?? mb_strtolower($titulo);
            if (isset($seenSec[$canon])) {
                continue;
            }
            $seenSec[$canon] = true;

            $items = [];
            $seenItem = [];

            foreach (($sec['items'] ?? []) as $it) {
                $nombre = trim((string)($it['nombre'] ?? ''));
                if ($nombre === '') {
                    continue;
                }

                $norm = $this->normalizeLite($nombre);
                if ($this->hasSimilarKey($seenItem, $norm)) {
                    continue;
                }
                $seenItem[$norm] = true;

                $res = trim((string)($it['resultado_sugerido'] ?? 'Revisado'));
                if (!in_array($res, self::RESULT_CATALOG, true)) {
                    $res = 'Revisado';
                }

                $items[] = [
                    'nombre' => $this->ucfirstSafe($nombre),
                    'resultado_sugerido' => $res,
                ];

                if (count($items) >= self::MAX_ITEMS_PER_SECTION) {
                    break;
                }
            }

            if (!empty($items)) {
                $secsOut[] = [
                    'titulo' => $this->ucfirstSafe($titulo),
                    'items' => $items,
                ];

                if (count($secsOut) >= self::MAX_SECTIONS) {
                    break;
                }
            }
        }

        $json['secciones'] = $this->filterPortItems($secsOut, $inputText);

        $json['acciones_sugeridas'] = $this->uniqueWithSimilarity(array_map('strval', $json['acciones_sugeridas'] ?? []));
        $json['riesgos_seguridad']  = $this->uniqueWithSimilarity(array_map('strval', $json['riesgos_seguridad'] ?? []));
        $json['notas'] = isset($json['notas']) && $json['notas'] !== '' ? (string) $json['notas'] : null;

        $diag = $json['diagnostico'] ?? [];

        $diag['hallazgos_probables'] = $this->uniqueWithSimilarity(array_map('strval', $diag['hallazgos_probables'] ?? []));
        $diag['hipotesis'] = (string)($diag['hipotesis'] ?? 'Diagnóstico preliminar.');
        $diag['causa_raiz_probable'] = (string)($diag['causa_raiz_probable'] ?? 'No determinada.');
        $diag['pruebas_sugeridas'] = $this->uniqueWithSimilarity(array_map('strval', $diag['pruebas_sugeridas'] ?? []));
        $diag['pasos_a_seguir'] = $this->uniqueWithSimilarity(array_map('strval', $diag['pasos_a_seguir'] ?? []));
        $diag['prioridad'] = in_array(($diag['prioridad'] ?? 'baja'), ['alta', 'media', 'baja'], true)
            ? $diag['prioridad']
            : 'baja';
        $diag['nivel_riesgo'] = in_array(($diag['nivel_riesgo'] ?? 'bajo'), ['alto', 'medio', 'bajo'], true)
            ? $diag['nivel_riesgo']
            : 'bajo';
        $diag['piezas_posibles'] = $this->uniqueWithSimilarity(array_map('strval', $diag['piezas_posibles'] ?? []));
        $diag['criterio_liberacion'] = $this->uniqueWithSimilarity(array_map('strval', $diag['criterio_liberacion'] ?? []));
        $diag['recomendacion_final'] = (string)($diag['recomendacion_final'] ?? 'Liberar solo si el equipo cumple con revisión funcional y de seguridad.');

        $diag['pruebas_detalladas'] = [];
        foreach (($json['diagnostico']['pruebas_detalladas'] ?? []) as $pd) {
            $nombre = trim((string)($pd['nombre'] ?? ''));
            if ($nombre === '') {
                continue;
            }

            $diag['pruebas_detalladas'][] = [
                'nombre' => $this->ucfirstSafe($nombre),
                'objetivo' => (string)($pd['objetivo'] ?? 'Validar comportamiento funcional del equipo.'),
                'procedimiento' => $this->uniqueWithSimilarity(array_map('strval', $pd['procedimiento'] ?? [])),
                'resultado_esperado' => (string)($pd['resultado_esperado'] ?? 'Resultado dentro de parámetros esperados.'),
            ];
        }

        $json['diagnostico'] = $diag;
        $json['resumen_ingenieria'] = $this->uniqueWithSimilarity(array_map('strval', $json['resumen_ingenieria'] ?? []));

        $json['remision_partidas'] = $this->sanitizePartidas(
            $json['remision_partidas'] ?? [],
            $json['acciones_sugeridas'] ?? [],
            $json['equipo']['nombre_detectado'] ?? 'equipo médico',
            $servicio
        );

        return $json;
    }

    private function sanitizePartidas(array $partidas, array $acciones, string $equipoNombre, string $servicio): array
    {
        $out = [];
        $seen = [];

        foreach ($partidas as $i => $p) {
            if (!is_array($p)) {
                continue;
            }

            $item = trim((string)($p['item'] ?? ('Partida ' . ($i + 1))));
            $descripcion = trim((string)($p['descripcion'] ?? ''));
            $unidad = trim((string)($p['unidad'] ?? 'SERVICIO'));
            $cantidad = (float)($p['cantidad'] ?? 1);
            $precio = (float)($p['precio_unitario'] ?? 0);

            if ($descripcion === '') {
                continue;
            }

            if ($cantidad <= 0) {
                $cantidad = 1;
            }

            if ($precio < 0) {
                $precio = 0;
            }

            $unidad = in_array($unidad, ['SERVICIO', 'PZA', 'KIT', 'LOTE'], true) ? $unidad : 'SERVICIO';

            $norm = $this->normalizeLite($descripcion);
            if ($this->hasSimilarKey($seen, $norm)) {
                continue;
            }
            $seen[$norm] = true;

            $out[] = [
                'item' => $item !== '' ? $item : ('Partida ' . (count($out) + 1)),
                'descripcion' => $this->ucfirstSafe($descripcion),
                'unidad' => $unidad,
                'cantidad' => $cantidad,
                'precio_unitario' => round($precio, 2),
            ];

            if (count($out) >= self::MAX_PARTIDAS) {
                break;
            }
        }

        if (!empty($out)) {
            return $out;
        }

        return $this->buildFallbackPartidasFromActions($acciones, $equipoNombre, $servicio);
    }

    private function buildFallbackPartidasFromActions(array $acciones, string $equipoNombre, string $servicio): array
    {
        $servicio = mb_strtolower(trim($servicio));
        $equipoNombre = trim($equipoNombre) !== '' ? trim($equipoNombre) : 'equipo médico';
        $accionesTxt = implode(', ', array_slice($acciones, 0, 4));

        if ($servicio === 'correctivo') {
            return [
                [
                    'item' => 'Partida 1',
                    'descripcion' => 'Diagnóstico técnico y revisión correctiva de ' . $equipoNombre,
                    'unidad' => 'SERVICIO',
                    'cantidad' => 1,
                    'precio_unitario' => 0,
                ],
                [
                    'item' => 'Partida 2',
                    'descripcion' => $accionesTxt !== ''
                        ? 'Corrección, ajuste o reparación realizada: ' . $accionesTxt
                        : 'Corrección, ajuste o reparación de falla detectada',
                    'unidad' => 'SERVICIO',
                    'cantidad' => 1,
                    'precio_unitario' => 0,
                ],
                [
                    'item' => 'Partida 3',
                    'descripcion' => 'Pruebas funcionales, validación operativa y liberación técnica',
                    'unidad' => 'SERVICIO',
                    'cantidad' => 1,
                    'precio_unitario' => 0,
                ],
            ];
        }

        if ($servicio === 'mixto') {
            return [
                [
                    'item' => 'Partida 1',
                    'descripcion' => 'Mantenimiento preventivo / correctivo de ' . $equipoNombre,
                    'unidad' => 'SERVICIO',
                    'cantidad' => 1,
                    'precio_unitario' => 0,
                ],
                [
                    'item' => 'Partida 2',
                    'descripcion' => $accionesTxt !== ''
                        ? 'Actividades realizadas durante el servicio: ' . $accionesTxt
                        : 'Limpieza, diagnóstico, ajuste y corrección según condición encontrada',
                    'unidad' => 'SERVICIO',
                    'cantidad' => 1,
                    'precio_unitario' => 0,
                ],
                [
                    'item' => 'Partida 3',
                    'descripcion' => 'Pruebas funcionales, validación final y entrega técnica',
                    'unidad' => 'SERVICIO',
                    'cantidad' => 1,
                    'precio_unitario' => 0,
                ],
            ];
        }

        return [
            [
                'item' => 'Partida 1',
                'descripcion' => 'Mantenimiento preventivo general de ' . $equipoNombre,
                'unidad' => 'SERVICIO',
                'cantidad' => 1,
                'precio_unitario' => 0,
            ],
            [
                'item' => 'Partida 2',
                'descripcion' => $accionesTxt !== ''
                    ? 'Limpieza, inspección y ajuste realizados: ' . $accionesTxt
                    : 'Limpieza, inspección, ajuste y revisión general',
                'unidad' => 'SERVICIO',
                'cantidad' => 1,
                'precio_unitario' => 0,
            ],
            [
                'item' => 'Partida 3',
                'descripcion' => 'Pruebas funcionales, validación final y liberación técnica',
                'unidad' => 'SERVICIO',
                'cantidad' => 1,
                'precio_unitario' => 0,
            ],
        ];
    }

    private function postClamp(array $json): array
    {
        $secs = $json['secciones'] ?? [];
        if (count($secs) > self::MAX_SECTIONS) {
            $secs = array_slice($secs, 0, self::MAX_SECTIONS);
        }

        foreach ($secs as &$s) {
            $items = $s['items'] ?? [];
            if (count($items) > self::MAX_ITEMS_PER_SECTION) {
                $items = array_slice($items, 0, self::MAX_ITEMS_PER_SECTION);
            }
            $s['items'] = $items;
        }
        unset($s);

        $json['secciones'] = $secs;

        if (empty($json['acciones_sugeridas'])) {
            $json['acciones_sugeridas'] = ['Verificación funcional', 'Registro en formato'];
        }

        if (empty($json['riesgos_seguridad'])) {
            $json['riesgos_seguridad'] = ['Desconectar de la red eléctrica antes de abrir el equipo'];
        }

        $json['diagnostico'] = $json['diagnostico'] ?? [
            'hallazgos_probables' => [],
            'hipotesis' => 'Revisión preventiva.',
            'causa_raiz_probable' => 'No determinada.',
            'pruebas_sugeridas' => ['Pruebas funcionales del fabricante'],
            'pruebas_detalladas' => [],
            'pasos_a_seguir' => ['Inspección visual', 'Prueba funcional', 'Documentación'],
            'prioridad' => 'baja',
            'nivel_riesgo' => 'bajo',
            'piezas_posibles' => [],
            'criterio_liberacion' => ['Pruebas funcionales satisfactorias'],
            'recomendacion_final' => 'Liberar solo si cumple pruebas mínimas.',
        ];

        if (empty($json['resumen_ingenieria'])) {
            $json['resumen_ingenieria'] = [
                'Inspección visual',
                'Pruebas funcionales',
                'Registro y liberación'
            ];
        }

        if (empty($json['remision_partidas'])) {
            $json['remision_partidas'] = $this->buildFallbackPartidasFromActions(
                $json['acciones_sugeridas'] ?? [],
                $json['equipo']['nombre_detectado'] ?? 'equipo médico',
                'preventivo'
            );
        }

        return $json;
    }

    private function maybeWithMeta(array $payload, array $meta, array $opts): array
    {
        if (!empty($opts['return_meta'])) {
            $payload['_meta'] = $meta;
        }

        return $payload;
    }

    private function sectionCanonicalSynonyms(): array
    {
        return [
            'conexiones' => 'conexiones',
            'puertos' => 'conexiones',
            'io' => 'conexiones',
            'entradas y salidas' => 'conexiones',

            'parametros' => 'parámetros',
            'parámetros' => 'parámetros',
            'seguridad' => 'seguridad',
            'alarmas' => 'seguridad',

            'osd y controles' => 'controles',
            'controles' => 'controles',
            'ui' => 'controles',
            'menú' => 'controles',
            'menu' => 'controles',

            'sensores y fugas' => 'sensores y fugas',
            'pruebas de fugas' => 'sensores y fugas',

            'integridad' => 'integridad',
            'estructura' => 'integridad',
            'chasis' => 'integridad',

            'sistema' => 'sistema',
        ];
    }

    private function normalizeLite(string $s): string
    {
        $s = Str::ascii($s);
        $s = mb_strtolower($s);
        $s = preg_replace('/[^a-z0-9\s]/i', ' ', $s);
        $s = preg_replace('/\s+/', ' ', trim($s));
        return $s;
    }

    private function hasSimilarKey(array $seen, string $norm): bool
    {
        foreach ($seen as $k => $_) {
            if ($k === $norm) {
                return true;
            }

            if (strlen($norm) > 6 && (str_contains($norm, $k) || str_contains($k, $norm))) {
                return true;
            }

            similar_text($k, $norm, $pct);
            if ($pct >= 85.0) {
                return true;
            }
        }

        return false;
    }

    private function uniqueWithSimilarity(array $arr): array
    {
        $out = [];
        $seen = [];

        foreach ($arr as $v) {
            $v = trim((string)$v);
            if ($v === '') {
                continue;
            }

            $norm = $this->normalizeLite($v);
            if ($this->hasSimilarKey($seen, $norm)) {
                continue;
            }

            $seen[$norm] = true;
            $out[] = $this->ucfirstSafe($v);
        }

        return $out;
    }

    private function arrayUniqueCI(array $arr): array
    {
        $seen = [];
        $out = [];

        foreach ($arr as $v) {
            $k = mb_strtolower(trim((string)$v));
            if ($k === '' || isset($seen[$k])) {
                continue;
            }

            $seen[$k] = true;
            $out[] = $this->ucfirstSafe((string)$v);
        }

        return $out;
    }

    private function ucfirstSafe(string $s): string
    {
        if ($s === '') {
            return $s;
        }

        $first = mb_substr($s, 0, 1);
        $rest  = mb_substr($s, 1);

        return mb_strtoupper($first) . $rest;
    }
}