<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

use App\Models\ChatMessage;
use App\Models\Cliente;
use App\Models\ChatFlow;

use App\Services\WhatsAppService;
use App\Services\OpenAIWhatsAppAgentService;
use App\Services\RemisionAiTools;

use Carbon\Carbon;

class WhatsappWebhookController extends Controller
{
    /** GET /api/webhooks/whatsapp (verificación de Meta) */
    public function verify(Request $req)
    {
        $token  = (string) config('services.whatsapp.verify_token');
        $mode   = $req->query('hub_mode', $req->query('hub.mode'));
        $verify = $req->query('hub_verify_token', $req->query('hub.verify_token'));
        $chall  = $req->query('hub_challenge', $req->query('hub.challenge'));

        if ($mode === 'subscribe' && hash_equals($token, (string) $verify)) {
            return response($chall, 200);
        }
        return response('Forbidden', 403);
    }

    /** POST /api/webhooks/whatsapp (eventos entrantes y estatus) */
    public function receive(Request $request)
    {
        $raw = $request->getContent();
        Log::info('WA_WEBHOOK_IN', ['size' => strlen($raw)]);

        $payload = json_decode($raw, true) ?: [];

        // Normaliza changes
        $changes = [];
        if (isset($payload['entry'][0]['changes'])) {
            foreach ($payload['entry'] as $entry) {
                foreach (($entry['changes'] ?? []) as $change) $changes[] = $change;
            }
        } elseif (isset($payload['field'], $payload['value'])) {
            $changes[] = $payload;
        } else {
            Log::warning('WA_WEBHOOK_UNKNOWN_SHAPE', ['keys' => array_keys($payload)]);
            return response()->json(['ok' => true]);
        }

        $wa      = app(WhatsAppService::class);
        $agent   = app(OpenAIWhatsAppAgentService::class);
        $remTool = app(RemisionAiTools::class);

        $ourE164 = preg_replace('/\D+/', '', (string) config('services.whatsapp.phone_e164'));
        $appTz   = config('app.timezone', 'UTC');

        foreach ($changes as $change) {
            $value = $change['value'] ?? [];

            /* ========== 1) Mensajes entrantes ========== */
            foreach (($value['messages'] ?? []) as $msg) {
                try {
                    $type  = $msg['type'] ?? 'text';
                    $from  = WhatsAppService::normalizeMsisdn((string)($msg['from'] ?? ''));
                    $to    = $ourE164;
                    $wamid = $msg['id'] ?? null;

                    // Dedupe por wamid (Meta reintenta a veces)
                    if ($this->wamidAlreadyProcessed($wamid)) {
                        Log::info('WA_SKIP_DUP_WAMID', ['wamid' => $wamid, 'from' => $from]);
                        continue;
                    }

                    // Timestamp WhatsApp (epoch UTC) -> zona app
                    $ts = isset($msg['timestamp'])
                        ? Carbon::createFromTimestamp((int) $msg['timestamp'], 'UTC')->setTimezone($appTz)
                        : now($appTz);

                    // Cliente placeholder
                    $cliente = $this->ensureCliente($from, $value);

                    // Flujo por número
                    $flow = ChatFlow::firstOrCreate(['from' => $from], ['step' => 'start']);
                    $ctx  = is_array($flow->context) ? $flow->context : [];

                    // Parsear contenido
                    [$storedType, $textRaw, $textNorm, $mediaId, $mediaLink, $filename] = $this->parseIncoming($msg);

                    // Guardar mensaje entrante
                    ChatMessage::updateOrCreate(
                        ['wamid' => $wamid],
                        [
                            'cliente_id'     => $cliente->id,
                            'from'           => $from,
                            'to'             => $to,
                            'direction'      => 'in',
                            'type'           => $storedType,
                            'text'           => $textRaw,
                            'media_id'       => $mediaId,
                            'media_link'     => $mediaLink,
                            'media_filename' => $filename,
                            'wa_timestamp'   => $ts,
                            'status'         => 'received',
                            'raw'            => $msg,
                        ]
                    );

                    // Si está en mano de asesor, no respondemos.
                    if (!$this->shouldBotReply($flow)) {
                        Log::info('BOT_SKIPPED_HANDOVER', ['from' => $from]);
                        continue;
                    }

                    // Rate limit
                    if ($this->rateLimited($from)) {
                        continue;
                    }

                    /* =========================
                     *  CSAT
                     * ========================= */
                    if (($flow->step ?? '') === 'csat_wait' || !empty(data_get($ctx, 'csat.pending'))) {
                        $score = $this->extractCsatScore($msg, $textNorm, $textRaw);

                        if ($score !== null) {
                            $ctx['csat'] = [
                                'pending' => false,
                                'score'   => $score,
                                'at'      => now($appTz)->toIso8601String(),
                            ];
                            $ctx['handover'] = false;
                            $this->flowSet($flow, 'ai', $ctx);

                            $wa->sendText($from, "¡Gracias por tu calificación ({$score}/5)! 💚\nSi necesitas algo más, escríbeme aquí.");
                            $this->storeOut($cliente->id, $to, $from, 'text', "Gracias CSAT {$score}/5", $appTz);

                            if (!empty($wamid)) $wa->markAsRead($wamid);
                            continue;
                        }

                        $wa->sendText($from, "Por favor responde con un número del *1* al *5* (siendo 5 excelente).");
                        $this->storeOut($cliente->id, $to, $from, 'text', "Solicitud CSAT: 1..5", $appTz);

                        if (!empty($wamid)) $wa->markAsRead($wamid);
                        continue;
                    }

                    /* =========================
                     *  Handover directo por texto
                     * ========================= */
                    if ($textNorm && $this->isAdvisorRequest($textNorm)) {
                        $this->flowSet($flow, 'espera_asesor', [
                            'handover' => true,
                            'handover_reason' => 'Usuario solicitó asesor',
                            'handover_summary' => 'El usuario pidió hablar con un asesor.',
                        ]);

                        $wa->sendText($from, "✅ En un momento un asesor te contactará.\nSi puedes, dime tu *nombre* y *ciudad*.");
                        $this->storeOut($cliente->id, $to, $from, 'text', 'Handover por solicitud de asesor.', $appTz);

                        if (!empty($wamid)) $wa->markAsRead($wamid);
                        continue;
                    }

                    /* =========================================================
                     * ✅ WIZARD: Remisión mantenimiento paso-a-paso
                     * ========================================================= */
                    if ($this->shouldHandleRemisionFlow($flow, $textNorm, $textRaw)) {
                        $handled = $this->handleRemisionFlowWizard(
                            wa: $wa,
                            remTool: $remTool,
                            flow: $flow,
                            currentCliente: $cliente,
                            from: $from,
                            to: $to,
                            appTz: $appTz,
                            wamid: $wamid,
                            storedType: $storedType,
                            textRaw: (string) $textRaw,
                            textNorm: (string) $textNorm
                        );

                        if ($handled) {
                            if (!empty($wamid)) $wa->markAsRead($wamid);
                            continue;
                        }
                    }

                    /* =========================
                     *  OpenAI decide qué hacer (general)
                     * ========================= */
                    $userTextForAI = $this->buildUserTextForAI(
                        $storedType,
                        $textRaw,
                        $mediaId,
                        $filename
                    );

                    $decision = $agent->decide($from, $userTextForAI);

                    if (!$decision) {
                        $wa->sendText($from, "Estoy teniendo un problema para responder 😕\nSi prefieres, escribe *asesor*.");
                        $this->storeOut($cliente->id, $to, $from, 'text', 'Fallback: OpenAI sin decisión.', $appTz);

                        if (!empty($wamid)) $wa->markAsRead($wamid);
                        continue;
                    }

                    $reply     = trim((string)($decision['reply_text'] ?? ''));
                    $handover  = (bool)($decision['handover'] ?? false);
                    $hReason   = (string)($decision['handover_reason'] ?? '');
                    $hSummary  = (string)($decision['handover_summary'] ?? '');

                    if ($handover) {
                        $ctx = is_array($flow->context) ? $flow->context : [];
                        $ctx['handover'] = true;
                        $ctx['handover_reason']  = $hReason !== '' ? $hReason : 'Escalado por IA';
                        $ctx['handover_summary'] = $hSummary;
                        $this->flowSet($flow, 'espera_asesor', $ctx);
                    } else {
                        $ctx = is_array($flow->context) ? $flow->context : [];
                        $ctx['handover'] = false;
                        $this->flowSet($flow, 'ai', $ctx);
                    }

                    if ($reply === '') {
                        $reply = "¿Me confirmas qué necesitas para ayudarte mejor?";
                    }

                    $wa->sendText($from, $reply);
                    $this->storeOut($cliente->id, $to, $from, 'text', $reply, $appTz);

                    if (!empty($wamid)) $wa->markAsRead($wamid);

                } catch (\Throwable $e) {
                    Log::error('WA_INCOMING_ERROR', [
                        'ex'   => $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                    ]);
                }
            }

            /* ========== 2) Estatus de mensajes salientes ========== */
            foreach (($value['statuses'] ?? []) as $st) {
                try {
                    $id     = $st['id']     ?? null;
                    $status = $st['status'] ?? null;

                    if ($id) {
                        ChatMessage::where('wamid', $id)->update(['status' => $status]);
                        Log::info('WA_STATUS_UPDATE', ['wamid' => $id, 'status' => $status]);
                    }
                } catch (\Throwable $e) {
                    Log::error('WA_STATUS_ERROR', [
                        'ex'   => $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                    ]);
                }
            }
        }

        return response()->json(['ok' => true]);
    }

    /* =================== Helpers =================== */

    private function parseIncoming(array $msg): array
    {
        $type = $msg['type'] ?? 'text';

        $storedType = $type;
        $textRaw = null;
        $textNorm = null;
        $mediaId = null;
        $mediaLink = null;
        $filename = null;

        if ($type === 'text') {
            $textRaw  = (string) data_get($msg, 'text.body', '');
            $textNorm = mb_strtolower(trim($textRaw));
        } elseif ($type === 'image') {
            $mediaId   = data_get($msg, 'image.id');
            $mediaLink = data_get($msg, 'image.link');
        } elseif ($type === 'document') {
            $mediaId   = data_get($msg, 'document.id');
            $mediaLink = data_get($msg, 'document.link');
            $filename  = data_get($msg, 'document.filename');
        } elseif ($type === 'interactive') {
            $itype = data_get($msg, 'interactive.type');
            if ($itype === 'button_reply') {
                $title   = (string) data_get($msg, 'interactive.button_reply.title', '');
                $id      = (string) data_get($msg, 'interactive.button_reply.id', $title);
                $textRaw = $title !== '' ? $title : $id;
                $textNorm = mb_strtolower(trim($id !== '' ? $id : $textRaw));
            } elseif ($itype === 'list_reply') {
                $title   = (string) data_get($msg, 'interactive.list_reply.title', '');
                $id      = (string) data_get($msg, 'interactive.list_reply.id', $title);
                $textRaw = $title !== '' ? $title : $id;
                $textNorm = mb_strtolower(trim($id !== '' ? $id : $textRaw));
            } else {
                $textRaw = 'interactive';
                $textNorm = 'interactive';
            }
            $storedType = 'text';
        } elseif ($type === 'button') {
            $btnText    = (string) data_get($msg, 'button.text', '');
            $btnPayload = (string) data_get($msg, 'button.payload', $btnText);
            $textRaw    = $btnText !== '' ? $btnText : $btnPayload;
            $textNorm   = mb_strtolower(trim($btnPayload !== '' ? $btnPayload : $textRaw));
            $storedType = 'text';
        }

        return [$storedType, $textRaw, $textNorm, $mediaId, $mediaLink, $filename];
    }

    private function buildUserTextForAI(string $storedType, ?string $textRaw, ?string $mediaId, ?string $filename): string
    {
        if ($storedType === 'text') {
            return trim((string)$textRaw);
        }

        if ($storedType === 'image') {
            return "El cliente envió una IMAGEN (media_id: {$mediaId}). Pídele que describa qué necesita o qué revisamos.";
        }

        if ($storedType === 'document') {
            $fn = $filename ? " Archivo: {$filename}." : '';
            return "El cliente envió un DOCUMENTO (media_id: {$mediaId}).{$fn} Pídele el objetivo (cotización, ficha, revisión, etc.).";
        }

        return "El cliente envió un mensaje tipo {$storedType}. Pide aclaración en texto.";
    }

    private function ensureCliente(string $msisdn, array $value): Cliente
    {
        $fullName = trim((string) data_get($value, 'contacts.0.profile.name', ''));

        $nombre = 'Desconocido';
        $apellido = '-';

        if ($fullName !== '') {
            $parts = preg_split('/\s+/', $fullName, 2);
            $nombre = $parts[0] ?? 'Desconocido';
            $apellido = $parts[1] ?? '-';
        }

        $existing = Cliente::where('telefono', $msisdn)->first();
        if ($existing) return $existing;

        $cliente = Cliente::firstOrCreate(
            ['telefono' => $msisdn],
            ['nombre' => $nombre, 'apellido' => $apellido, 'comentarios' => 'Creado desde WhatsApp']
        );

        if (empty($cliente->comentarios)) {
            $cliente->comentarios = 'Creado desde WhatsApp';
            $cliente->save();
        }

        return $cliente;
    }

    private function shouldBotReply(ChatFlow $flow): bool
    {
        $ctx = is_array($flow->context) ? $flow->context : [];
        if (!empty($ctx['handover'])) return false;
        if (($flow->step ?? '') === 'espera_asesor') return false;
        return true;
    }

    private function flowSet(ChatFlow $flow, string $step, array $mergeCtx = []): void
    {
        $ctx = is_array($flow->context) ? $flow->context : [];
        if (!empty($mergeCtx)) $ctx = array_merge($ctx, $mergeCtx);

        $flow->step = $step;
        $flow->context = $ctx;
        $flow->save();
    }

    private function storeOut(int $clienteId, string $fromOur, string $toUser, string $type, string $text, string $appTz): void
    {
        ChatMessage::create([
            'wamid'        => uniqid('out_'),
            'cliente_id'   => $clienteId,
            'from'         => $fromOur,
            'to'           => $toUser,
            'direction'    => 'out',
            'type'         => $type,
            'text'         => $text,
            'wa_timestamp' => now($appTz),
            'status'       => 'sent',
        ]);
    }

    private function wamidAlreadyProcessed(?string $wamid): bool
    {
        if (!$wamid) return false;
        $k = 'wa_wamid:' . $wamid;
        return !Cache::add($k, 1, now()->addHours(6));
    }

    private function rateLimited(string $msisdn): bool
    {
        $sec = (int) env('WA_AI_RATE_LIMIT_SECONDS', 2);
        $k = 'wa_ai_rl:' . preg_replace('/\D+/', '', $msisdn);

        if (Cache::has($k)) return true;
        Cache::put($k, 1, $sec);

        return false;
    }

    private function isAdvisorRequest(string $textNorm): bool
    {
        return (bool) preg_match('/\b(asesor|humano|agente|persona|llámame|llamar|marcar|quiero hablar con)\b/u', $textNorm);
    }

    private function extractCsatScore(array $msg, ?string $textNorm, ?string $textRaw): ?int
    {
        $payload = mb_strtolower(trim((string) data_get($msg, 'button.payload', data_get($msg, 'button.text', ''))));
        $t = $payload !== '' ? $payload : mb_strtolower(trim((string)($textNorm ?? $textRaw ?? '')));

        if (in_array($t, ['1','2','3','4','5'], true)) return (int) $t;

        if (str_contains($t, 'excelente')) return 5;
        if (str_contains($t, 'buena')) return 3;
        if (str_contains($t, 'mala')) return 1;

        return null;
    }

    /* =========================================================
     * ✅ REMISIÓN: Wizard (paso por paso)
     * ========================================================= */

    private function shouldHandleRemisionFlow(ChatFlow $flow, ?string $textNorm, ?string $textRaw): bool
    {
        $step = (string)($flow->step ?? '');
        if (str_starts_with($step, 'remwiz_')) return true;

        $t = mb_strtolower(trim((string)($textNorm ?: $textRaw ?: '')));
        if ($t === '') return false;

        // intención
        return (bool) preg_match('/\b(remisi[oó]n|orden de servicio|pdf|mantenimiento)\b/u', $t);
    }

    private function handleRemisionFlowWizard(
        WhatsAppService $wa,
        RemisionAiTools $remTool,
        ChatFlow $flow,
        Cliente $currentCliente,
        string $from,
        string $to,
        string $appTz,
        ?string $wamid,
        string $storedType,
        string $textRaw,
        string $textNorm
    ): bool {
        // ✅ Allowlist
        if (!$this->isRemisionAllowedByMsisdn($from)) {
            $wa->sendText(
                $from,
                "⚠️ Este número no tiene permiso para generar *remisión de mantenimiento*.\nSi necesitas apoyo, escribe *asesor*."
            );
            $this->storeOut($currentCliente->id, $to, $from, 'text', 'Remisión bloqueada por allowlist.', $appTz);

            $ctx = is_array($flow->context) ? $flow->context : [];
            unset($ctx['remwiz']);
            $this->flowSet($flow, 'ai', $ctx);
            return true;
        }

        // cancelar
        if (preg_match('/\b(cancelar|cancela|olvida|salir)\b/u', $textNorm)) {
            $ctx = is_array($flow->context) ? $flow->context : [];
            unset($ctx['remwiz']);
            $this->flowSet($flow, 'ai', $ctx);

            $wa->sendText($from, "✅ Listo, cancelé la remisión. ¿En qué más te ayudo?");
            $this->storeOut($currentCliente->id, $to, $from, 'text', 'Canceló remisión wizard.', $appTz);
            return true;
        }

        // si no es texto, pedir texto
        if ($storedType !== 'text') {
            $wa->sendText($from, "Para generar la remisión necesito que me respondas en *texto* 🙏");
            $this->storeOut($currentCliente->id, $to, $from, 'text', 'Remisión wizard: pidió texto.', $appTz);
            return true;
        }

        $ctx = is_array($flow->context) ? $flow->context : [];
        $draft = is_array($ctx['remwiz'] ?? null) ? $ctx['remwiz'] : [];

        // init draft
        $draft = array_merge([
            'cliente_id' => 0,
            'cliente_nombre' => '',
            'cliente_apellido' => '',
            'cliente_telefono' => '',
            'cliente_email' => '',
            'cliente_direccion' => '',

            'cantidad' => 0,
            'unidad' => '',
            'nombre_item' => '',
            'descripcion_item' => '',
            'importe_unitario' => 0,

            'aplicar_iva' => -1, // -1 desconocido, 0 no, 1 sí
            'tiene_envio' => -1, // -1 desconocido, 0 no, 1 sí
            'envio_direccion' => '',
            'envio_costo' => 0,
        ], $draft);

        $step = (string)($flow->step ?? '');

        // si aún no está en wizard, iniciar
        if (!str_starts_with($step, 'remwiz_')) {
            // Tel por defecto: del WhatsApp
            $digits = preg_replace('/\D+/', '', (string)$from);
            $last10 = substr($digits, -10);
            $draft['cliente_telefono'] = $draft['cliente_telefono'] ?: $last10;

            $ctx['remwiz'] = $draft;
            $this->flowSet($flow, 'remwiz_cliente', $ctx);

            $wa->sendText($from, "🧾 *Remisión de mantenimiento*\n\n1/9) ¿A nombre de quién va la remisión? (Nombre y apellido)");
            $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard start: pidió cliente.', $appTz);
            return true;
        }

        // ---------- Paso 1: Cliente ----------
        if ($step === 'remwiz_cliente') {
            $name = trim($textRaw);
            if (mb_strlen($name) < 3) {
                $wa->sendText($from, "Porfa envíame el *nombre y apellido* (ej: Juan Pérez).");
                $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard cliente inválido.', $appTz);
                return true;
            }

            // guardar nombre/apellido (simple)
            $parts = preg_split('/\s+/', $name, 2);
            $draft['cliente_nombre'] = trim($parts[0] ?? '');
            $draft['cliente_apellido'] = trim($parts[1] ?? '');

            $ctx['remwiz'] = $draft;
            $this->flowSet($flow, 'remwiz_cantidad', $ctx);

            $wa->sendText($from, "2/9) ¿Cantidad? (ej: 1)");
            $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard: pidió cantidad.', $appTz);
            return true;
        }

        // ---------- Paso 2: Cantidad ----------
        if ($step === 'remwiz_cantidad') {
            $n = $this->parseInt($textRaw);
            if ($n < 1) {
                $wa->sendText($from, "Dime solo la *cantidad* (número). Ej: 1");
                $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard cantidad inválida.', $appTz);
                return true;
            }

            $draft['cantidad'] = $n;

            $ctx['remwiz'] = $draft;
            $this->flowSet($flow, 'remwiz_unidad', $ctx);

            $wa->sendText($from, "3/9) ¿Unidad? (ej: servicio / pieza / paquete)");
            $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard: pidió unidad.', $appTz);
            return true;
        }

        // ---------- Paso 3: Unidad ----------
        if ($step === 'remwiz_unidad') {
            $u = trim($textRaw);
            if ($u === '') {
                $wa->sendText($from, "Escribe la *unidad* (ej: servicio).");
                $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard unidad inválida.', $appTz);
                return true;
            }

            $draft['unidad'] = mb_substr($u, 0, 40);

            $ctx['remwiz'] = $draft;
            $this->flowSet($flow, 'remwiz_nombre', $ctx);

            $wa->sendText($from, "4/9) ¿Nombre del concepto?\nEj: *Mantenimiento preventivo a colonoscopio*");
            $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard: pidió nombre_item.', $appTz);
            return true;
        }

        // ---------- Paso 4: Nombre concepto ----------
        if ($step === 'remwiz_nombre') {
            $t = trim($textRaw);
            if (mb_strlen($t) < 3) {
                $wa->sendText($from, "Escribe el *nombre del concepto* (mínimo 3 letras).");
                $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard nombre_item inválido.', $appTz);
                return true;
            }

            $draft['nombre_item'] = mb_substr($t, 0, 180);

            $ctx['remwiz'] = $draft;
            $this->flowSet($flow, 'remwiz_desc', $ctx);

            $wa->sendText($from, "5/9) ¿Descripción del servicio? (qué se hizo / qué se hará)");
            $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard: pidió descripcion_item.', $appTz);
            return true;
        }

        // ---------- Paso 5: Descripción ----------
        if ($step === 'remwiz_desc') {
            $d = trim($textRaw);
            if (mb_strlen($d) < 3) {
                $wa->sendText($from, "Escribe una *descripción* (mínimo 3 letras).");
                $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard descripcion inválida.', $appTz);
                return true;
            }

            $draft['descripcion_item'] = mb_substr($d, 0, 700);

            $ctx['remwiz'] = $draft;
            $this->flowSet($flow, 'remwiz_precio', $ctx);

            $wa->sendText($from, "6/9) ¿Precio unitario? (ej: 3000 o $3000)");
            $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard: pidió importe_unitario.', $appTz);
            return true;
        }

        // ---------- Paso 6: Precio unitario ----------
        if ($step === 'remwiz_precio') {
            $p = $this->parseMoney($textRaw);
            if ($p <= 0) {
                $wa->sendText($from, "Envíame el *precio unitario* (ej: 3000 o $3000).");
                $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard precio inválido.', $appTz);
                return true;
            }

            $draft['importe_unitario'] = $p;

            $ctx['remwiz'] = $draft;
            $this->flowSet($flow, 'remwiz_iva', $ctx);

            $wa->sendText($from, "7/9) ¿Aplica IVA? Responde *sí* o *no*.");
            $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard: pidió IVA.', $appTz);
            return true;
        }

        // ---------- Paso 7: IVA ----------
        if ($step === 'remwiz_iva') {
            $yn = $this->parseYesNo($textNorm);
            if ($yn === null) {
                $wa->sendText($from, "Responde solo: *sí* o *no* (IVA).");
                $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard IVA inválido.', $appTz);
                return true;
            }

            $draft['aplicar_iva'] = $yn ? 1 : 0;

            $ctx['remwiz'] = $draft;
            $this->flowSet($flow, 'remwiz_envio', $ctx);

            $wa->sendText($from, "8/9) ¿Aplica envío? Responde *sí* o *no*.");
            $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard: pidió envío.', $appTz);
            return true;
        }

        // ---------- Paso 8: Envío ----------
        if ($step === 'remwiz_envio') {
            $yn = $this->parseYesNo($textNorm);
            if ($yn === null) {
                $wa->sendText($from, "Responde solo: *sí* o *no* (envío).");
                $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard envío inválido.', $appTz);
                return true;
            }

            $draft['tiene_envio'] = $yn ? 1 : 0;

            if (!$yn) {
                // finalizar sin envío
                return $this->finalizeRemisionWizard($wa, $remTool, $flow, $currentCliente, $from, $to, $appTz, $draft);
            }

            $ctx['remwiz'] = $draft;
            $this->flowSet($flow, 'remwiz_envio_dir', $ctx);

            $wa->sendText($from, "9/10) ¿Dirección de envío? (calle, ciudad, etc.)");
            $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard: pidió dirección envío.', $appTz);
            return true;
        }

        // ---------- Paso 9: Dirección envío ----------
        if ($step === 'remwiz_envio_dir') {
            $dir = trim($textRaw);
            if (mb_strlen($dir) < 5) {
                $wa->sendText($from, "Escribe la *dirección de envío* (mínimo 5 letras).");
                $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard dirección inválida.', $appTz);
                return true;
            }

            $draft['envio_direccion'] = mb_substr($dir, 0, 220);

            $ctx['remwiz'] = $draft;
            $this->flowSet($flow, 'remwiz_envio_costo', $ctx);

            // ✅ costo al final
            $wa->sendText($from, "10/10) ¿Costo del envío? (ej: 250 o $250)");
            $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard: pidió costo envío.', $appTz);
            return true;
        }

        // ---------- Paso 10: Costo envío ----------
        if ($step === 'remwiz_envio_costo') {
            $c = $this->parseMoney($textRaw);
            if ($c < 0) $c = 0;
            if ($c <= 0) {
                $wa->sendText($from, "Envíame el *costo del envío* (ej: 250 o $250).");
                $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard costo envío inválido.', $appTz);
                return true;
            }

            $draft['envio_costo'] = $c;

            return $this->finalizeRemisionWizard($wa, $remTool, $flow, $currentCliente, $from, $to, $appTz, $draft);
        }

        // si cae aquí, reinicia suave
        $ctx['remwiz'] = $draft;
        $this->flowSet($flow, 'remwiz_cliente', $ctx);
        $wa->sendText($from, "Vamos a iniciar de nuevo 🙂\n1/9) ¿A nombre de quién va la remisión? (Nombre y apellido)");
        $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard reset.', $appTz);
        return true;
    }

    private function finalizeRemisionWizard(
        WhatsAppService $wa,
        RemisionAiTools $remTool,
        ChatFlow $flow,
        Cliente $currentCliente,
        string $from,
        string $to,
        string $appTz,
        array $draft
    ): bool {
        // construir args finales
        $digits = preg_replace('/\D+/', '', (string)$from);
        $last10 = substr($digits, -10);

        $cantidad = max(1, (int)($draft['cantidad'] ?? 1));
        $unidad   = trim((string)($draft['unidad'] ?? 'servicio')) ?: 'servicio';
        $nombre   = trim((string)($draft['nombre_item'] ?? 'Servicio')) ?: 'Servicio';
        $desc     = trim((string)($draft['descripcion_item'] ?? ''));
        $precio   = (float)($draft['importe_unitario'] ?? 0);

        if ($precio <= 0) $precio = 0;

        $args = [
            'cliente_id' => 0, // no lo exigimos; RemisionAiTools puede crear/actualizar con teléfono

            'cliente_nombre'    => (string)($draft['cliente_nombre'] ?? ''),
            'cliente_apellido'  => (string)($draft['cliente_apellido'] ?? ''),
            'cliente_telefono'  => (string)($draft['cliente_telefono'] ?? $last10),
            'cliente_email'     => (string)($draft['cliente_email'] ?? ''),
            'cliente_direccion' => (string)($draft['cliente_direccion'] ?? ''),

            'items' => [
                [
                    'cantidad' => $cantidad,
                    'unidad' => $unidad,
                    'nombre_item' => $nombre,
                    'descripcion_item' => $desc,
                    'importe_unitario' => $precio,
                ]
            ],

            'aplicar_iva' => ((int)($draft['aplicar_iva'] ?? 0)) === 1,
            'tiene_envio' => ((int)($draft['tiene_envio'] ?? 0)) === 1,
            'envio_costo' => (float)($draft['envio_costo'] ?? 0),
            'envio_direccion' => (string)($draft['envio_direccion'] ?? ''),

            // ya no obligamos meta_pairs aquí
            'meta_pairs' => [],
        ];

        $created = $remTool->createMaintenanceRemision($from, $args);

        if (!($created['ok'] ?? false)) {
            $wa->sendText($from, "No pude generar la remisión 😕\nEscribe *remisión* para intentarlo de nuevo o *asesor*.");
            $this->storeOut($currentCliente->id, $to, $from, 'text', 'Wizard: create failed.', $appTz);

            $ctx = is_array($flow->context) ? $flow->context : [];
            unset($ctx['remwiz']);
            $this->flowSet($flow, 'ai', $ctx);
            return true;
        }

        $absPath  = (string)($created['pdf_abs_path'] ?? '');
        $fileName = (string)($created['pdf_filename'] ?? ('remision_'.$created['remision_id'].'.pdf'));
        $signed   = (string)($created['pdf_signed_url'] ?? '');

        try {
            if ($absPath !== '' && is_file($absPath)) {
                $up = $wa->uploadMediaPath($absPath, $fileName, 'application/pdf');

                if ($up->ok() && ($mediaIdUp = (string)($up->json('id') ?? ''))) {
                    $caption = "📄 *Remisión de mantenimiento* #".($created['remision_id'] ?? '')."\n"
                        . "Total: $".number_format((float)($created['total'] ?? 0), 2)."\n"
                        . (!empty($created['importe_letra']) ? ("(".($created['importe_letra']).")\n") : '')
                        . "Gracias por confiar en *Grupo MediBuy*.";

                    $wa->sendDocumentById($from, $mediaIdUp, $fileName, $caption);
                    $this->storeOut($currentCliente->id, $to, $from, 'document', "PDF enviado: {$fileName}", $appTz);

                    $wa->sendText($from, "✅ Listo. Te envié tu *PDF* de la remisión #".($created['remision_id'] ?? '').".");
                    $this->storeOut($currentCliente->id, $to, $from, 'text', "Confirmación PDF remisión #".($created['remision_id'] ?? ''), $appTz);

                    $ctx = is_array($flow->context) ? $flow->context : [];
                    unset($ctx['remwiz']);
                    $this->flowSet($flow, 'ai', $ctx);
                    return true;
                }
            }

            $fallback = "✅ Remisión generada (#".($created['remision_id'] ?? '').").\n"
                . "No pude adjuntar el PDF, pero puedes descargarlo aquí (24h):\n"
                . ($signed ?: '(sin link)');

            $wa->sendText($from, $fallback);
            $this->storeOut($currentCliente->id, $to, $from, 'text', $fallback, $appTz);

            $ctx = is_array($flow->context) ? $flow->context : [];
            unset($ctx['remwiz']);
            $this->flowSet($flow, 'ai', $ctx);
            return true;

        } catch (\Throwable $e) {
            Log::error('WA_SEND_REMISION_PDF_FAIL', [
                'remision_id' => $created['remision_id'] ?? null,
                'err' => $e->getMessage(),
            ]);

            $fallback = "✅ Remisión generada (#".($created['remision_id'] ?? '').").\n"
                . "Hubo un problema al adjuntar el PDF. Descárgalo aquí (24h):\n"
                . ($signed ?: '(sin link)');

            $wa->sendText($from, $fallback);
            $this->storeOut($currentCliente->id, $to, $from, 'text', $fallback, $appTz);

            $ctx = is_array($flow->context) ? $flow->context : [];
            unset($ctx['remwiz']);
            $this->flowSet($flow, 'ai', $ctx);
            return true;
        }
    }

    /**
     * ✅ Allowlist: solo estos números generan remisión
     * .env => WA_MAINT_REMISION_ALLOWLIST=7224485191,2205381046,7224407379
     */
    private function isRemisionAllowedByMsisdn(string $msisdn): bool
    {
        $allow = trim((string) env('WA_MAINT_REMISION_ALLOWLIST', ''));
        if ($allow === '') return false;

        $digits = preg_replace('/\D+/', '', $msisdn);
        $last10 = substr($digits, -10);

        $list = array_filter(array_map(function ($x) {
            return substr(preg_replace('/\D+/', '', trim($x)), -10);
        }, explode(',', $allow)));

        return in_array($last10, $list, true);
    }

    private function parseYesNo(string $textNorm): ?bool
    {
        $t = trim(mb_strtolower($textNorm));

        if (in_array($t, ['si','sí','s','ok','va','dale','claro','afirmativo'], true)) return true;
        if (in_array($t, ['no','n','nel','negativo'], true)) return false;

        // frases
        if (str_contains($t, 'con iva')) return true;
        if (str_contains($t, 'sin iva')) return false;

        return null;
    }

    private function parseInt(string $text): int
    {
        $t = preg_replace('/[^\d]/', ' ', $text);
        if (preg_match('/\b(\d{1,6})\b/', $t, $m)) {
            return (int)$m[1];
        }
        return 0;
    }

    private function parseMoney(string $text): float
    {
        $t = str_replace([',', ' '], '', trim($text));
        // $3000, 3000, 3000.50
        if (preg_match('/\$?(\d+(?:\.\d{1,2})?)/', $t, $m)) {
            return (float)$m[1];
        }
        return 0.0;
    }

    /* ============================================================
     * Reclamar conversación por un agente (handover ON)
     * ============================================================ */
    public function claimByAgent(Request $request, string $msisdn)
    {
        $agentId   = $request->integer('agent_id');
        $agentName = trim((string) $request->input('agent_name'));

        $flow = ChatFlow::firstOrCreate(
            ['from' => WhatsAppService::normalizeMsisdn($msisdn)],
            ['step' => 'start']
        );

        $ctx = is_array($flow->context) ? $flow->context : [];
        $ctx['handover'] = true;

        if ($agentId)   $ctx['agent']['id']   = $agentId;
        if ($agentName) $ctx['agent']['name'] = $agentName;

        $flow->step    = 'espera_asesor';
        $flow->context = $ctx;
        $flow->save();

        return response()->json(['ok' => true, 'flow' => $flow]);
    }

    /* ============================================================
     * Cerrar conversación (handover OFF) + enviar CSAT (plantilla)
     * ============================================================ */
    public function closeByAgent(Request $request, string $msisdn)
    {
        $appTz = config('app.timezone', 'UTC');
        $wa    = app(WhatsAppService::class);

        $toUser = WhatsAppService::normalizeMsisdn($msisdn);
        $flow   = ChatFlow::firstOrCreate(['from' => $toUser], ['step' => 'start']);
        $ctx    = is_array($flow->context) ? $flow->context : [];

        $ctx['handover'] = false;
        $ctx['csat'] = ['pending' => true];

        $flow->step    = 'csat_wait';
        $flow->context = $ctx;
        $flow->save();

        $cliente = Cliente::where('telefono', $toUser)->first();
        $clienteNombre = trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? '')) ?: 'Cliente';

        $agentNameReq  = trim((string) $request->input('agent_name'));
        $agentNameCtx  = (string) data_get($ctx, 'agent.name', '');
        $asesorNombre  = $agentNameReq !== '' ? $agentNameReq : ($agentNameCtx !== '' ? $agentNameCtx : 'nuestro equipo');

        $lang = $wa->pickTemplateLanguage('csat_soporte_postchat') ?? 'es_MX';
        $res  = $wa->sendTemplateText($toUser, 'csat_soporte_postchat', $lang, [$clienteNombre, $asesorNombre]);

        Log::info('WA_CSAT_TEMPLATE_SENT', ['to'=>$toUser, 'lang'=>$lang, 'resp'=>$res->json()]);

        $our = (string) config('services.whatsapp.phone_e164');
        $cid = optional($cliente)->id;

        if ($cid) {
            $this->storeOut($cid, preg_replace('/\D+/', '', $our), $toUser, 'text', 'Solicitud de CSAT (plantilla)', $appTz);
        }

        return response()->json(['ok' => true]);
    }
}
