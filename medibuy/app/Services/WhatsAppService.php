<?php
// app/Services/WhatsAppService.php

namespace App\Services;

use App\Models\Evento; // <-- agregado (para el atajo desde Evento)
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon; // <-- agregado (para formatear fecha)

class WhatsAppService
{
    protected string $token;
    protected string $version;
    protected string $phoneId;
    protected string $wabaId;

    public function __construct()
    {
        $cfg = config('services.whatsapp');
        $this->token   = (string) ($cfg['token'] ?? '');
        $this->version = (string) ($cfg['version'] ?? 'v21.0'); // ajusta si usas otra versión
        $this->phoneId = (string) ($cfg['phone_id'] ?? '');
        $this->wabaId  = (string) ($cfg['waba_id'] ?? '');
    }

    /** Endpoint base para el phone number id */
    protected function endpoint(string $path = 'messages'): string
    {
        return "https://graph.facebook.com/{$this->version}/{$this->phoneId}/{$path}";
    }

    /** Cliente JSON con token */
    protected function httpJson(): PendingRequest
    {
        return Http::withToken($this->token)
            ->acceptJson()
            ->asJson()
            ->timeout(30);
    }

    /* -------------------------------------------------------------
     |            SUBIDA DE ARCHIVOS / MEDIA
     * ------------------------------------------------------------*/

    /** Subir archivo desde <input type="file"> a /media */
    public function uploadMedia(UploadedFile $file): Response
    {
        return Http::withToken($this->token)
            ->acceptJson()
            ->timeout(60)
            ->attach(
                'file',
                fopen($file->getRealPath(), 'r'),
                $file->getClientOriginalName(),
                ['Content-Type' => $file->getMimeType() ?: 'application/octet-stream']
            )
            ->post($this->endpoint('media'), [
                'messaging_product' => 'whatsapp',
                'type' => $file->getMimeType() ?: 'application/octet-stream',
            ]);
    }

    /** Subir archivo por ruta absoluta (ej. PDF generado) a /media */
    public function uploadMediaPath(string $absolutePath, string $filename, ?string $mime = null): Response
    {
        if (!is_readable($absolutePath)) {
            throw new \RuntimeException("Archivo no legible: {$absolutePath}");
        }

        $req = Http::withToken($this->token)
            ->acceptJson()
            ->timeout(60);

        $headers = [];
        if ($mime) $headers['Content-Type'] = $mime;

        $req = $req->attach('file', fopen($absolutePath, 'r'), $filename, $headers);

        $body = ['messaging_product' => 'whatsapp'];
        if ($mime) $body['type'] = $mime; // p.ej. application/pdf

        return $req->post($this->endpoint('media'), $body);
    }

    /* -------------------------------------------------------------
     |            ENVÍOS LIBRES (no plantilla)
     * ------------------------------------------------------------*/

    /** Enviar imagen por media_id (modo libre, no plantilla) */
    public function sendImageByIdPromotion(string $to, string $mediaId, string $title, string $description): Response
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'to'   => $to,
            'type' => 'image',
            'image' => [
                'id'      => $mediaId,
                'caption' => "*{$title}*\n\n{$description}",
            ],
        ];
        return $this->httpJson()->post($this->endpoint(), $payload);
    }

    /** Enviar DOCUMENTO por media_id (no plantilla) */
    public function sendDocumentById(string $to, string $mediaId, ?string $filename = null, ?string $caption = null): Response
    {
        $doc = ['id' => $mediaId];
        if ($filename) $doc['filename'] = $filename;
        if ($caption)  $doc['caption']  = $caption;

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'   => $to,
            'type' => 'document',
            'document' => $doc,
        ];
        return $this->httpJson()->post($this->endpoint(), $payload);
    }

    /** Enviar texto libre (pruebas rápidas) */
    public function sendText(string $to, string $text): Response
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'to'   => $to,
            'type' => 'text',
            'text' => ['body' => $text],
        ];
        return $this->httpJson()->post($this->endpoint(), $payload);
    }

    /* -------------------------------------------------------------
     |            INTERACTIVOS (Botones y Listas)
     * ------------------------------------------------------------*/

    /**
     * Enviar BOTONES (Reply Buttons).
     * $buttons: [['id'=>'cotizar','title'=>'Cotizar'], ...] máx 3
     */
    public function sendInteractiveButtons(string $to, string $bodyText, array $buttons, ?string $footerText = null): Response
    {
        // WhatsApp permite HASTA 3 botones
        $buttons = array_slice($buttons, 0, 3);

        $btns = [];
        foreach ($buttons as $btn) {
            if (!isset($btn['id'], $btn['title'])) continue;
            $btns[] = [
                'type'  => 'reply',
                'reply' => [
                    'id'    => (string) $btn['id'],
                    'title' => (string) $btn['title'],
                ],
            ];
        }

        // Seguridad: al menos 1
        if (empty($btns)) {
            return $this->sendText($to, $bodyText);
        }

        $interactive = [
            'type'   => 'button',
            'body'   => ['text' => $bodyText],
            'action' => ['buttons' => $btns],
        ];
        if ($footerText) $interactive['footer'] = ['text' => $footerText];

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'          => $to,
            'type'        => 'interactive',
            'interactive' => $interactive,
        ];

        $res = $this->httpJson()->post($this->endpoint(), $payload);
        if ($res->failed()) {
            Log::error('WA_SEND_BUTTONS_FAIL', ['to'=>$to, 'status'=>$res->status(), 'resp'=>$res->json()]);
        } else {
            Log::info('WA_SEND_BUTTONS_OK', ['to'=>$to, 'resp'=>$res->json()]);
        }
        return $res;
    }

    /**
     * Enviar LISTA (List Message).
     * $sections = [
     *   [
     *     'title' => 'Equipos',
     *     'rows'  => [
     *        ['id'=>'torre','title'=>'Torre de Laparoscopía','description'=>'...'],
     *        ...
     *     ]
     *   ],
     *   ...
     * ]
     */
    public function sendInteractiveList(string $to, string $bodyText, string $buttonText, array $sections, ?string $footerText = null): Response
    {
        $interactive = [
            'type'   => 'list',
            'body'   => ['text' => $bodyText],
            'action' => [
                'button'   => $buttonText,
                'sections' => $sections,
            ],
        ];
        if ($footerText) $interactive['footer'] = ['text' => $footerText];

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'          => $to,
            'type'        => 'interactive',
            'interactive' => $interactive,
        ];

        $res = $this->httpJson()->post($this->endpoint(), $payload);
        if ($res->failed()) {
            Log::error('WA_SEND_LIST_FAIL', [
                'to'=>$to, 'status'=>$res->status(), 'resp'=>$res->json(), 'payload'=>$payload,
            ]);
        } else {
            Log::info('WA_SEND_LIST_OK', ['to'=>$to, 'resp'=>$res->json()]);
        }
        return $res;
    }

    /** Menú principal prearmado (Cotizar / Comprar / Info) + recordatorio de "asesor" por texto */
    public function sendMainMenu(string $to): Response
    {
        $text = "👋 ¡Hola! Soy tu asistente MediBuy.\nElige una opción:";
        $buttons = [
            ['id' => 'cotizar', 'title' => '💰 Cotizar'],
            ['id' => 'comprar', 'title' => '🛒 Comprar'],
            ['id' => 'info',    'title' => 'ℹ️ Info equipo'],
        ];
        return $this->sendInteractiveButtons($to, $text."\n\nSi prefieres, responde *asesor*.", $buttons, "MediBuy");
    }

    /**
     * Extrae una respuesta interactiva del payload del webhook.
     * Retorna:
     *  - null si no es interactivo
     *  - ['type'=>'button'|'list','id'=>..., 'title'=>..., 'from'=>..., 'wamid'=>..., 'timestamp'=>...]
     */
    public static function parseInteractiveReply(array $payload): ?array
    {
        $msg = data_get($payload, 'entry.0.changes.0.value.messages.0');
        if (!$msg || ($msg['type'] ?? null) !== 'interactive') return null;

        $from      = data_get($msg, 'from');
        $wamid     = data_get($msg, 'id');
        $timestamp = data_get($msg, 'timestamp');

        $itype = data_get($msg, 'interactive.type');
        if ($itype === 'button_reply') {
            $id    = data_get($msg, 'interactive.button_reply.id');
            $title = data_get($msg, 'interactive.button_reply.title');
            return [
                'type'      => 'button',
                'id'        => $id,
                'title'     => $title,
                'from'      => $from,
                'wamid'     => $wamid,
                'timestamp' => $timestamp,
            ];
        }

        if ($itype === 'list_reply') {
            $id    = data_get($msg, 'interactive.list_reply.id');
            $title = data_get($msg, 'interactive.list_reply.title');
            return [
                'type'      => 'list',
                'id'        => $id,
                'title'     => $title,
                'from'      => $from,
                'wamid'     => $wamid,
                'timestamp' => $timestamp,
            ];
        }

        return null;
    }

    /* -------------------------------------------------------------
     |            ENVÍOS CON PLANTILLA (TEMPLATES)
     * ------------------------------------------------------------*/

    /**
     * Plantilla GENÉRICA (soporta HEADER TEXT opcional y botones URL).
     * Ej: $wa->sendTemplate($to, 'pago_recordatorio_v1', 'es_MX', $body, $header, $urls);
     */
    public function sendTemplate(
        string $to,
        string $templateName,
        string $langCode,
        array $bodyParams = [],
        array $headerTextParams = [],     // si tu template tiene HEADER TEXT con variables
        array $urlButtonsSuffixes = []    // si tu template tiene botones URL
    ): Response {
        $components = [];

        // HEADER TEXT (variables)
        if (!empty($headerTextParams)) {
            $hp = [];
            foreach ($headerTextParams as $p) {
                $hp[] = ['type' => 'text', 'text' => (string)$p];
            }
            $components[] = ['type' => 'header', 'parameters' => $hp];
        }

        // BODY
        if (!empty($bodyParams)) {
            $bp = [];
            foreach ($bodyParams as $p) {
                $bp[] = ['type' => 'text', 'text' => (string)$p];
            }
            $components[] = ['type' => 'body', 'parameters' => $bp];
        }

        // Botones URL (opcionales)
        foreach ($urlButtonsSuffixes as $index => $suffix) {
            $components[] = [
                'type' => 'button',
                'sub_type' => 'url',
                'index' => (string) $index,
                'parameters' => [['type' => 'text', 'text' => (string) $suffix]],
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $langCode ?: 'es_MX'],
                'components' => $components,
            ],
        ];

        $res = $this->httpJson()->post($this->endpoint(), $payload);
        if ($res->failed()) {
            Log::error('WA_TEMPLATE_SEND_FAIL', [
                'template' => $templateName,
                'to'       => $to,
                'status'   => $res->status(),
                'resp'     => $res->json(),
                'payload'  => $payload,
            ]);
        } else {
            Log::info('WA_TEMPLATE_SEND_OK', [
                'template' => $templateName,
                'to'       => $to,
                'resp'     => $res->json(),
            ]);
        }
        return $res;
    }

    /**
     * Plantilla flexible con HEADER DOCUMENT + BODY (0..n params) + botones URL opcionales.
     */
    public function sendTemplateWithDocument(
        string $to,
        string $templateName,
        string $langCode,
        string $mediaId,
        string $filename,
        ?string $clienteNombre = null, // {{1}}
        ?string $frase = null,         // {{2}}
        ?string $btn0UrlSuffix = null, // botón URL index 0
        ?string $btn1UrlSuffix = null  // botón URL index 1
    ): Response {
        // HEADER: documento
        $components = [[
            'type' => 'header',
            'parameters' => [[
                'type' => 'document',
                'document' => [
                    'id'       => $mediaId,
                    'filename' => $filename,
                ],
            ]],
        ]];

        // BODY: sólo params no vacíos para evitar mismatch
        $bodyParams = [];
        if (!is_null($clienteNombre) && $clienteNombre !== '') {
            $bodyParams[] = ['type' => 'text', 'text' => $clienteNombre];
        }
        if (!is_null($frase) && $frase !== '') {
            $bodyParams[] = ['type' => 'text', 'text' => $frase];
        }
        if (!empty($bodyParams)) {
            $components[] = ['type' => 'body', 'parameters' => $bodyParams];
        }

        // Botones URL opcionales
        if ($btn0UrlSuffix) {
            $components[] = [
                'type' => 'button',
                'sub_type' => 'url',
                'index' => '0',
                'parameters' => [['type' => 'text', 'text' => $btn0UrlSuffix]],
            ];
        }
        if ($btn1UrlSuffix) {
            $components[] = [
                'type' => 'button',
                'sub_type' => 'url',
                'index' => '1',
                'parameters' => [['type' => 'text', 'text' => $btn1UrlSuffix]],
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $langCode ?: 'es_MX'],
                'components' => $components,
            ],
        ];

        return $this->httpJson()->post($this->endpoint(), $payload);
    }

    /**
     * Plantilla optimizada para 1 variable en el BODY (HEADER DOCUMENT + {{1}}).
     */
    public function sendTemplateDoc1Var(
        string $to,
        string $templateName,
        string $langCode,
        string $mediaId,
        string $filename,
        string $var1Text // {{1}}
    ): Response {
        $components = [
            [
                'type' => 'header',
                'parameters' => [[
                    'type' => 'document',
                    'document' => [
                        'id'       => $mediaId,
                        'filename' => $filename,
                    ],
                ]],
            ],
            [
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $var1Text],
                ],
            ],
        ];

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $langCode ?: 'es_MX'],
                'components' => $components,
            ],
        ];

        return $this->httpJson()->post($this->endpoint(), $payload);
    }

    /**
     * Atajo (opcional) para la plantilla de servicio al admin con botón a detalle.
     * Plantilla: alerta_solicitud_admin_svc_v1 (es_MX)
     * Vars: {{1}} ID, {{2}} Solicitante, {{3}} Detalle, {{4}} Fecha
     * Botón URL base configurado en Meta: .../solicitudes/admin/ + {{1}}
     */
    public function sendAdminAlertTemplate(
        string $to,
        int|string $id,
        string $solicitante,
        string $detalle,
        string $fecha,
        ?string $langCode = null
    ): Response {
        $tpl  = 'alerta_solicitud_admin_svc_v1';
        $lang = $langCode ?: ($this->pickTemplateLanguage($tpl) ?? 'es_MX');

        return $this->sendTemplateText(
            $to,
            $tpl,
            $lang,
            [$id, $solicitante, $detalle, $fecha],
            [$id] // sufijo para el botón URL index 0
        );
    }

    /**
     * Plantilla de TEXTO (sin header) con variables en el BODY.
     */
    public function sendTemplateText(
        string $to,
        string $templateName,
        string $langCode,
        array $bodyParams = [],
        array $buttonUrlSuffixes = []
    ): Response {
        $components = [];

        if (!empty($bodyParams)) {
            $params = [];
            foreach ($bodyParams as $p) {
                $params[] = ['type' => 'text', 'text' => (string) $p];
            }
            $components[] = ['type' => 'body', 'parameters' => $params];
        }

        // URL buttons con suffix dinámico (si la plantilla tiene botones URL)
        foreach ($buttonUrlSuffixes as $index => $suffix) {
            $components[] = [
                'type' => 'button',
                'sub_type' => 'url',
                'index' => (string) $index,
                'parameters' => [['type' => 'text', 'text' => (string) $suffix]],
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $langCode ?: 'es_MX'],
                'components' => $components,
            ],
        ];

        $res = $this->httpJson()->post($this->endpoint(), $payload);
        if ($res->failed()) {
            Log::error('WA_TEMPLATE_SEND_FAIL', [
                'template' => $templateName,
                'to'       => $to,
                'status'   => $res->status(),
                'resp'     => $res->json(),
            ]);
        } else {
            Log::info('WA_TEMPLATE_SEND_OK', [
                'template' => $templateName,
                'to'       => $to,
                'resp'     => $res->json(),
            ]);
        }
        return $res;
    }

    /* -------------------------------------------------------------
     |        ENVÍOS CON PLANTILLA: HEADER IMAGEN (promo_todo)
     * ------------------------------------------------------------*/

    /** Plantilla con HEADER IMAGEN + BODY */
    public function sendTemplateWithImage(
        string $to,
        string $templateName,
        string $langCode,
        string $mediaId,
        ?string $var1 = null, // {{1}}
        ?string $var2 = null  // {{2}}
    ): Response {
        $components = [
            [
                'type' => 'header',
                'parameters' => [[
                    'type'  => 'image',
                    'image' => ['id' => $mediaId],
                ]],
            ],
        ];

        $body = [];
        if (!is_null($var1) && $var1 !== '') $body[] = ['type' => 'text', 'text' => $var1];
        if (!is_null($var2) && $var2 !== '') $body[] = ['type' => 'text', 'text' => $var2];
        if (!empty($body)) $components[] = ['type' => 'body', 'parameters' => $body];

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name'      => $templateName,
                'language'  => ['code' => $langCode ?: 'es_MX'],
                'components'=> $components,
            ],
        ];

        $res = $this->httpJson()->post($this->endpoint(), $payload);
        if ($res->failed()) {
            Log::error('WA_TPL_IMG_FAIL', ['to'=>$to,'tpl'=>$templateName,'resp'=>$res->json()]);
        }
        return $res;
    }

    /** Atajo para tu plantilla "promo_todo" */
    public function sendPromoTodo(string $to, string $mediaId, string $nombre, string $frase): Response
    {
        $lang = $this->pickTemplateLanguage('promo_todo') ?? 'es_MX';
        return $this->sendTemplateWithImage($to, 'promo_todo', $lang, $mediaId, $nombre, $frase);
    }

    /* -------------------------------------------------------------
     |                    UTILIDADES
     * ------------------------------------------------------------*/

    /** Normaliza MX a E.164 (10 dígitos -> 521 + número) */
    public static function normalizeMsisdn(string $raw): string
    {
        $digits = preg_replace('/\D+/', '', $raw);
        if (Str::startsWith($digits, '521') || Str::startsWith($digits, '52')) return $digits;
        if (strlen($digits) === 10) return '521' . $digits;
        return $digits;
    }

    /** Info del número (sirve para confirmar a qué WABA pertenece) */
    public function getPhoneNumberMeta(): array
    {
        $url = "https://graph.facebook.com/{$this->version}/{$this->phoneId}?fields=id,display_phone_number,verified_name";
        $res = Http::withToken($this->token)->acceptJson()->get($url);
        return $res->json();
    }

    /** Lista plantillas (WABA + Phone) y devuelve sólo aprobadas en español. */
    public function fetchTemplatesSmart(int $limit = 200): array
    {
        $items = [];

        // Por WABA
        if (!empty($this->wabaId)) {
            $r = Http::withToken($this->token)
                ->acceptJson()
                ->get("https://graph.facebook.com/{$this->version}/{$this->wabaId}/message_templates", [
                    'limit' => $limit,
                ]);
            if ($r->ok()) {
                $items = array_merge($items, data_get($r->json(), 'data', []));
            } else {
                Log::warning('WABA_TPL_LIST_FAIL', ['resp' => $r->json()]);
            }
        }

        // (Algunas cuentas exponen también por Phone Number ID)
        $r2 = Http::withToken($this->token)
            ->acceptJson()
            ->get("https://graph.facebook.com/{$this->version}/{$this->phoneId}/message_templates", [
                'limit' => $limit,
            ]);
        if ($r2->ok()) {
            $items = array_merge($items, data_get($r2->json(), 'data', []));
        } else {
            Log::warning('PHONE_TPL_LIST_FAIL', ['resp' => $r2->json()]);
        }

        // Filtrar duplicados, aprobadas y en español
        $seen = [];
        $out  = [];
        foreach ($items as $it) {
            if (!is_array($it)) continue;
            $name = $it['name']     ?? null;
            $lang = $it['language'] ?? null;
            $stat = $it['status']   ?? null;
            if (!$name || !$lang || $stat !== 'APPROVED') continue;
            if (!str_starts_with($lang, 'es')) continue; // es, es_MX, es_ES, es_LA...

            $key = strtolower($name.'|'.$lang);
            if (isset($seen[$key])) continue;
            $seen[$key] = true;

            $out[] = [
                'name'     => $name,
                'language' => $lang,
                'category' => $it['category'] ?? null,
            ];
        }

        return $out;
    }

    /** Agrupa por nombre -> [languages...] */
    public function groupTemplatesByName(array $tpls): array
    {
        $map = [];
        foreach ($tpls as $t) {
            $n = $t['name']; $l = $t['language'];
            if (!isset($map[$n])) $map[$n] = [];
            if (!in_array($l, $map[$n], true)) $map[$n][] = $l;
        }
        foreach ($map as &$langs) { sort($langs); }
        ksort($map);
        return $map;
    }

    /** Info de idiomas disponibles para un nombre de plantilla */
    public function getTemplateInfo(string $templateName): ?array
    {
        $items = $this->fetchTemplatesSmart(200);
        $byName = array_values(array_filter($items, function ($it) use ($templateName) {
            return is_array($it) && strcasecmp($it['name'] ?? '', $templateName) === 0;
        }));

        if (!$byName) return null;

        $languages = array_values(array_unique(array_map(fn($x) => $x['language'], $byName)));

        return [
            'name'      => $templateName,
            'languages' => $languages,
        ];
    }

    /** Elige primer idioma disponible según preferencia */
    public function pickTemplateLanguage(string $templateName, array $preferred = ['es_MX','es','es_ES','es_LA']): ?string
    {
        $info = $this->getTemplateInfo($templateName);
        if (!$info) return null;

        foreach ($preferred as $code) {
            if (in_array($code, $info['languages'] ?? [], true)) return $code;
        }
        return $info['languages'][0] ?? null;
    }

    /** Marcar como leído un mensaje entrante (del cliente) */
    public function markAsRead(string $wamid): Response
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'status'     => 'read',
            'message_id' => $wamid,
        ];
        return $this->httpJson()->post($this->endpoint(), $payload);
    }

    /* =============================================================
     |   *** AÑADIDOS SIN ROMPER TUS PLANTILLAS EXISTENTES ***
     * ============================================================*/

    /**
     * Envía una plantilla de SERVICIO (texto, sin botones).
     * Útil para recordatorios. Si no pasas idioma, intenta elegir uno válido.
     * $vars: por ejemplo [nombre, titulo, fecha, ubicacion, notas, anticipacion]
     */
    public function sendServiceReminder(
        string $to,
        string $templateName,
        ?string $langCode,
        array $vars
    ): Response {
        $lang = $langCode ?: ($this->pickTemplateLanguage($templateName) ?? (config('services.whatsapp.service_template_lang') ?: 'es_MX'));
        $to   = self::normalizeMsisdn($to);

        return $this->sendTemplateText(
            $to,
            $templateName,
            $lang,
            $vars,
            /* sin botones */ []
        );
    }

    /**
     * Atajo: arma las 6 variables desde un Evento.
     * Vars esperadas por tu plantilla de servicio (ajústala en Meta):
     *   {{1}} nombre, {{2}} título, {{3}} fecha, {{4}} ubicación, {{5}} notas, {{6}} anticipación
     *
     * $templateName por defecto toma services.whatsapp.service_template_name o 'servicio_recordatorio_evento'
     * $anticipacion: "3 días", "2 días", "1 día" o "hoy"
     */
    public function sendServiceReminderFromEvento(
        string $to,
        Evento $evento,
        string $destinatarioNombre,
        string $anticipacion,
        ?string $templateName = null,
        ?string $langCode = null,
        ?string $timezone = null
    ): Response {
        $tplName = $templateName ?: (config('services.whatsapp.service_template_name') ?: 'servicio_recordatorio_evento');
        $fecha   = $this->formatFechaEs($evento->start, $timezone);

        $vars = [
            $destinatarioNombre ?: '¡Hola!',
            (string)($evento->title ?? 'Evento'),
            $fecha,
            (string)($evento->location ?? '-'),
            (string)($evento->notes ?? '-'),
            $anticipacion ?: 'hoy',
        ];

        return $this->sendServiceReminder($to, $tplName, $langCode, $vars);
    }

    /**
     * Fecha/hora en español legible: "vie 05 sep 2025, 09:00"
     */
    protected function formatFechaEs($dateTime, ?string $tz = null): string
    {
        try {
            $dt = Carbon::parse($dateTime);
            if ($tz) $dt->setTimezone($tz);
            else     $dt->setTimezone(config('app.timezone'));
            return $dt->locale('es')->translatedFormat('D d MMM yyyy, HH:mm');
        } catch (\Throwable $e) {
            return (string)$dateTime;
        }
    }
}
