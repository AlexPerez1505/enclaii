<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

use App\Models\ChatMessage;
use App\Models\Cliente;
use App\Models\ChatFlow;

use App\Services\WhatsAppService;
use App\Services\OpenAIWhatsAppAgentService;

use Carbon\Carbon;

class WhatsappInboxController extends Controller
{
    /** Bandeja de entrada: conversaciones agrupadas por msisdn (contraparte) */
    public function index()
    {
        $ourE164 = preg_replace('/\D+/', '', (string) config('services.whatsapp.phone_e164'));

        $base = ChatMessage::query()
            ->select([
                DB::raw("CASE WHEN REPLACE(`from`, '+', '') <> '{$ourE164}' THEN `from` ELSE `to` END AS msisdn"),
                DB::raw('MAX(wa_timestamp) as last_at'),
            ])
            ->groupBy('msisdn')
            ->orderByDesc('last_at')
            ->get();

        $threads = $base->map(function ($row) use ($ourE164) {
            $msisdn = $row->msisdn;

            $lastMsg = ChatMessage::where(function ($q) use ($msisdn) {
                    $q->where('from', $msisdn)->orWhere('to', $msisdn);
                })
                ->orderBy('wa_timestamp', 'desc')
                ->first();

            $digits = preg_replace('/\D+/', '', (string) $msisdn);
            $last10 = substr($digits, -10);
            $cliente = $last10
                ? Cliente::where('telefono', 'like', "%{$last10}%")->first()
                : null;

            $displayName = $cliente
                ? (trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? '')) ?: $msisdn)
                : $msisdn;

            $flow = ChatFlow::where('from', $msisdn)->first();
            $ctx  = is_array($flow?->context) ? $flow->context : [];

            $agentName = data_get($ctx, 'agent.name');
            $handover  = !empty($ctx['handover']) || (($flow->step ?? '') === 'espera_asesor');
            $handoverReason  = (string) ($ctx['handover_reason'] ?? '');
            $handoverSummary = (string) ($ctx['handover_summary'] ?? '');

            $unread = ChatMessage::where(function ($q) use ($msisdn) {
                    $q->where('from', $msisdn)->orWhere('to', $msisdn);
                })
                ->where('direction', 'in')
                ->where(function ($q) {
                    $q->whereNull('status')->orWhere('status', '<>', 'read');
                })
                ->count();

            return (object) [
                'msisdn'          => $msisdn,
                'display_name'    => $displayName,
                'last_text'       => $lastMsg?->text ?? null,
                'last_type'       => $lastMsg?->type ?? null,
                'last_at'         => $row->last_at,
                'agent_name'      => $agentName,
                'handover'        => $handover,
                'handover_reason' => $handoverReason,
                'handover_summary'=> $handoverSummary,
                'unread_count'    => $unread,
            ];
        });

        return view('whatsapp.inbox', compact('threads'));
    }

    /** Mostrar el chat con un número específico */
    public function show($msisdn)
    {
        $appTz = config('app.timezone', 'UTC');

        $messages = ChatMessage::where(function ($q) use ($msisdn) {
                $q->where('from', $msisdn)->orWhere('to', $msisdn);
            })
            ->orderBy('wa_timestamp', 'asc')
            ->get()
            ->map(function ($m) {
                if (in_array($m->type, ['image', 'document'])) {
                    $m->media_link = $m->media_id
                        ? (\Illuminate\Support\Facades\Route::has('wa.media') ? route('wa.media', $m->media_id) : $m->media_link)
                        : $m->media_link;
                }
                return $m;
            });

        // Marcar como leídos localmente (cuando abren el chat)
        $this->markThreadRead($msisdn);

        $digits = preg_replace('/\D+/', '', (string) $msisdn);
        $last10 = substr($digits, -10);
        $cliente = $last10
            ? Cliente::where('telefono', 'like', "%{$last10}%")->first()
            : null;

        $displayName = $cliente
            ? (trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? '')) ?: $msisdn)
            : $msisdn;

        $flow = ChatFlow::firstOrCreate(['from' => $msisdn], ['step' => 'start']);
        $ctx  = is_array($flow->context) ? $flow->context : [];

        $agentName = data_get($ctx, 'agent.name');
        $handover  = !empty($ctx['handover']) || (($flow->step ?? '') === 'espera_asesor');

        $ourE164 = preg_replace('/\D+/', '', (string) config('services.whatsapp.phone_e164'));

$conversations = ChatMessage::query()
    ->select([
        DB::raw("CASE WHEN REPLACE(`from`, '+', '') <> '{$ourE164}' THEN `from` ELSE `to` END AS wa_user"),
        DB::raw('MAX(wa_timestamp) as last_at'),
    ])
    ->groupBy('wa_user')
    ->orderByDesc('last_at')
    ->get()
    ->map(function ($row) {

        $last = ChatMessage::where('from',$row->wa_user)
                    ->orWhere('to',$row->wa_user)
                    ->latest('wa_timestamp')
                    ->first();

        return (object)[
            'wa_user' => $row->wa_user,
            'display_name' => $row->wa_user,
            'last_message' => $last?->text ?? '',
            'last_at' => $row->last_at
        ];
    });

$currentConversation = $conversations
    ->firstWhere('wa_user', $msisdn);

        return view('whatsapp.chat', [
            'messages'        => $messages,
            'currentUser'     => $msisdn,
            'displayName'     => $displayName,
            'agentName'       => $agentName,
            'handover'        => $handover,
            'handoverReason'  => (string)($ctx['handover_reason'] ?? ''),
            'handoverSummary' => (string)($ctx['handover_summary'] ?? ''),
            'flowStep'        => (string)($flow->step ?? 'start'),
            'conversations' => $conversations,
'currentConversation' => $currentConversation, 
        ]);
    }

    /**
     * TOMAR conversación por agente (handover ON) sin necesidad de enviar mensaje
     * POST /whatsapp/inbox/{msisdn}/claim
     */
    public function claim(Request $request, $msisdn)
    {
        $to = WhatsAppService::normalizeMsisdn((string)$msisdn);

        $flow = ChatFlow::firstOrCreate(['from' => $to], ['step' => 'start']);
        $ctx  = is_array($flow->context) ? $flow->context : [];

        $ctx['handover'] = true;
        $ctx['handover_reason']  = 'Agente tomó la conversación';
        $ctx['handover_summary'] = $ctx['handover_summary'] ?? '';

        if ($user = Auth::user()) {
            $ctx['agent'] = [
                'id'   => $user->id,
                'name' => $user->name ?? ('Agente #' . $user->id),
            ];
        }

        $flow->step    = 'espera_asesor';
        $flow->context = $ctx;
        $flow->save();

        return response()->json(['ok' => true, 'handover' => true, 'agent' => $ctx['agent'] ?? null]);
    }

    /**
     * LIBERAR conversación a IA (handover OFF)
     * POST /whatsapp/inbox/{msisdn}/release
     */
    public function release(Request $request, $msisdn)
    {
        $to = WhatsAppService::normalizeMsisdn((string)$msisdn);

        $flow = ChatFlow::firstOrCreate(['from' => $to], ['step' => 'start']);
        $ctx  = is_array($flow->context) ? $flow->context : [];

        $ctx['handover'] = false;
        $ctx['handover_reason']  = null;
        $ctx['handover_summary'] = null;
        // opcional: limpiar agente
        // unset($ctx['agent']);

        $flow->step    = 'ai';
        $flow->context = $ctx;
        $flow->save();

        return response()->json(['ok' => true, 'handover' => false]);
    }

    /**
     * SUGERENCIA IA (NO envía, solo devuelve texto para que el asesor lo vea/copypaste)
     * POST /whatsapp/inbox/{msisdn}/ai-suggest
     */
    public function aiSuggest(Request $request, $msisdn, OpenAIWhatsAppAgentService $agent)
    {
        $to = WhatsAppService::normalizeMsisdn((string)$msisdn);

        $history = ChatMessage::where(function ($q) use ($to) {
                $q->where('from', $to)->orWhere('to', $to);
            })
            ->orderBy('wa_timestamp', 'desc')
            ->limit((int) env('WA_AI_HISTORY_LIMIT', 20))
            ->get()
            ->reverse()
            ->values();

        $prompt = $this->buildHistoryPrompt($history, $to);

        $decision = $agent->decide($to, $prompt);

        return response()->json([
            'ok'       => true,
            'handover' => (bool)($decision['handover'] ?? false),
            'reason'   => (string)($decision['handover_reason'] ?? ''),
            'summary'  => (string)($decision['handover_summary'] ?? ''),
            'reply'    => trim((string)($decision['reply_text'] ?? '')),
        ]);
    }

    /**
     * ENVIAR con IA (envía directo al cliente y guarda en BD). No toma handover.
     * POST /whatsapp/inbox/{msisdn}/ai-send
     */
    public function aiSend(Request $request, $msisdn, OpenAIWhatsAppAgentService $agent, WhatsAppService $wa)
    {
        $request->validate([
            'message' => ['nullable','string'],
        ]);

        $to = WhatsAppService::normalizeMsisdn((string)$msisdn);
        $appTz = config('app.timezone', 'UTC');
        $fromE164 = preg_replace('/\D+/', '', (string) config('services.whatsapp.phone_e164'));

        // Si mandan "message", lo usamos como último input; si no, usamos historial.
        $seed = trim((string)$request->input('message', ''));
        if ($seed === '') {
            $history = ChatMessage::where(function ($q) use ($to) {
                    $q->where('from', $to)->orWhere('to', $to);
                })
                ->orderBy('wa_timestamp', 'desc')
                ->limit((int) env('WA_AI_HISTORY_LIMIT', 20))
                ->get()
                ->reverse()
                ->values();

            $seed = $this->buildHistoryPrompt($history, $to);
        }

        $decision = $agent->decide($to, $seed);
        $reply = trim((string)($decision['reply_text'] ?? ''));

        if ($reply === '') {
            return response()->json(['ok' => false, 'message' => 'IA no devolvió respuesta.'], 422);
        }

        // Si la IA pide handover, NO enviamos como IA (o puedes enviar mensaje de “te paso con asesor”).
        if (!empty($decision['handover'])) {
            $flow = ChatFlow::firstOrCreate(['from' => $to], ['step' => 'start']);
            $ctx  = is_array($flow->context) ? $flow->context : [];
            $ctx['handover'] = true;
            $ctx['handover_reason']  = (string)($decision['handover_reason'] ?? 'Escalado por IA');
            $ctx['handover_summary'] = (string)($decision['handover_summary'] ?? '');
            $flow->step = 'espera_asesor';
            $flow->context = $ctx;
            $flow->save();

            return response()->json([
                'ok' => true,
                'handover' => true,
                'reply' => null,
                'reason' => $ctx['handover_reason'],
                'summary'=> $ctx['handover_summary'],
            ]);
        }

        // Enviar por WhatsApp
        $res = $wa->sendText($to, $reply);
        $json = $res->json();
        $wamid = data_get($json, 'messages.0.id') ?? uniqid('wamid_ai_');

        // Guardar en BD como OUT
        $chat = ChatMessage::create([
            'wamid'        => $wamid,
            'from'         => $fromE164,
            'to'           => $to,
            'direction'    => 'out',
            'type'         => 'text',
            'text'         => $reply,
            'wa_timestamp' => now($appTz),
            'status'       => 'sent',
            'raw'          => $json,
        ]);

        // Asegura handover OFF (IA activa)
        $flow = ChatFlow::firstOrCreate(['from' => $to], ['step' => 'start']);
        $ctx  = is_array($flow->context) ? $flow->context : [];
        $ctx['handover'] = false;
        $flow->step = 'ai';
        $flow->context = $ctx;
        $flow->save();

        return response()->json(['ok' => true, 'sent' => true, 'message' => $this->presentMessageForFront($chat, $appTz)]);
    }

    /** Enviar mensaje (texto, imagen o documento) — MANUAL (asesor) */
    public function send(Request $request, $msisdn)
    {
        $request->validate([
            'type' => 'required|string|in:text,image,document',
            'text' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10 MB
        ]);

        $type = $request->input('type');
        if ($type === 'text') {
            $request->validate(['text' => 'required|string']);
        } else {
            $request->validate(['file' => 'required|file|max:10240']);
        }

        $to       = WhatsAppService::normalizeMsisdn((string) $msisdn);
        $wa       = app(WhatsAppService::class);
        $appTz    = config('app.timezone', 'UTC');
        $fromE164 = preg_replace('/\D+/', '', (string) config('services.whatsapp.phone_e164'));

        $mediaId  = null;
        $filename = null;

        if ($type === 'text') {
            $res = $wa->sendText($to, (string)$request->input('text'));
        } else {
            $upload = $wa->uploadMedia($request->file('file'));
            $mediaId = data_get($upload->json(), 'id');

            if (!$upload->successful() || !$mediaId) {
                Log::error('WA_UPLOAD_FAIL', ['resp' => $upload->json()]);
                return response()->json(['message' => 'No se pudo subir el archivo a WhatsApp.'], 422);
            }

            $filename = $request->file('file')->getClientOriginalName();

            if ($type === 'image') {
                // Envío simple sin caption
                $payload = [
                    'messaging_product' => 'whatsapp',
                    'to'   => $to,
                    'type' => 'image',
                    'image'=> ['id' => $mediaId],
                ];
                $res = $this->waPost($payload);
            } else {
                $res = $wa->sendDocumentById($to, $mediaId, $filename);
            }
        }

        $json = $res->json();
        Log::info('WA_SEND_RES', ['res' => $json]);

        $wamid = data_get($json, 'messages.0.id') ?? uniqid('wamid_');

        // Reclamar conversación por el asesor que envía (handover ON)
        $flow = ChatFlow::firstOrCreate(['from' => $to], ['step' => 'start']);
        $ctx  = is_array($flow->context) ? $flow->context : [];
        $ctx['handover'] = true;

        if ($user = Auth::user()) {
            $ctx['agent'] = [
                'id'   => $user->id,
                'name' => $user->name ?? ('Agente #' . $user->id),
            ];
        }

        $flow->step    = 'espera_asesor';
        $flow->context = $ctx;
        $flow->save();

        $chat = ChatMessage::create([
            'wamid'          => $wamid,
            'from'           => $fromE164,
            'to'             => $to,
            'direction'      => 'out',
            'type'           => $type,
            'text'           => $type === 'text' ? (string)$request->input('text') : null,
            'media_id'       => $mediaId,
            'media_filename' => $filename,
            'wa_timestamp'   => now($appTz),
            'status'         => 'sent',
            'raw'            => $json,
        ]);

        if ($request->ajax()) {
            return response()
                ->json($this->presentMessageForFront($chat, $appTz))
                ->header('Cache-Control','no-store, no-cache, must-revalidate');
        }

        return back()->with('status', 'Mensaje enviado');
    }

    /** Helper para postear a WA usando el mismo token/version/phoneId (solo si ocupas payload custom) */
    private function waPost(array $payload)
    {
        $cfg     = config('services.whatsapp');
        $token   = (string) ($cfg['token'] ?? '');
        $version = (string) ($cfg['api_version'] ?? $cfg['version'] ?? 'v21.0');
        $phoneId = (string) ($cfg['phone_id'] ?? '');

        $endpoint = "https://graph.facebook.com/{$version}/{$phoneId}/messages";

        return \Illuminate\Support\Facades\Http::withToken($token)
            ->acceptJson()
            ->asJson()
            ->timeout(30)
            ->post($endpoint, $payload);
    }

    /** Polling del chat: mensajes de una conversación (ISO UTC) */
    public function fetch($msisdn)
    {
        $appTz = config('app.timezone', 'UTC');

        $messages = ChatMessage::where(function ($q) use ($msisdn) {
                $q->where('from', $msisdn)->orWhere('to', $msisdn);
            })
            ->orderBy('wa_timestamp', 'asc')
            ->get()
            ->map(function ($m) use ($appTz) {
                if (in_array($m->type, ['image', 'document'])) {
                    $m->media_link = $m->media_id
                        ? (\Illuminate\Support\Facades\Route::has('wa.media') ? route('wa.media', $m->media_id) : $m->media_link)
                        : $m->media_link;
                }
                try {
                    $m->wa_timestamp = Carbon::parse($m->wa_timestamp, $appTz)->utc()->toIso8601String();
                } catch (\Throwable $e) {}
                return $m;
            });

        return response()
            ->json($messages)
            ->header('Cache-Control','no-store, no-cache, must-revalidate');
    }

    /** Polling de bandeja: lista de hilos recientes (ETag/304) */
    public function fetchThreads(Request $request)
    {
        $ourE164  = preg_replace('/\D+/', '', (string) config('services.whatsapp.phone_e164'));
        $sinceIso = $request->query('since');
        $appTz    = config('app.timezone', 'UTC');

        $sinceApp = null;
        if ($sinceIso) {
            try { $sinceApp = Carbon::parse($sinceIso)->setTimezone($appTz); } catch (\Throwable $e) {}
        }

        $q = ChatMessage::query()
            ->select([
                DB::raw("CASE WHEN REPLACE(`from`, '+', '') <> '{$ourE164}' THEN `from` ELSE `to` END AS msisdn"),
                DB::raw('MAX(wa_timestamp) as last_at'),
            ])
            ->when($sinceApp, function ($qq) use ($sinceApp) {
                $qq->where('wa_timestamp', '>', $sinceApp->format('Y-m-d H:i:s'));
            })
            ->groupBy('msisdn')
            ->orderByDesc('last_at')
            ->limit(80);

        $base = $q->get();

        $rows = $base->map(function ($row) use ($appTz) {
            $msisdn = $row->msisdn;

            $lastMsg = ChatMessage::where(function ($q) use ($msisdn) {
                    $q->where('from', $msisdn)->orWhere('to', $msisdn);
                })
                ->orderBy('wa_timestamp', 'desc')
                ->first();

            $digits = preg_replace('/\D+/', '', (string) $msisdn);
            $last10 = substr($digits, -10);
            $cliente = $last10
                ? Cliente::where('telefono', 'like', "%{$last10}%")->first()
                : null;

            $display = $cliente
                ? (trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? '')) ?: $msisdn)
                : $msisdn;

            $flow = ChatFlow::where('from', $msisdn)->first();
            $ctx  = is_array($flow?->context) ? $flow->context : [];
            $agentName = data_get($ctx, 'agent.name');
            $handover  = !empty($ctx['handover']) || (($flow->step ?? '') === 'espera_asesor');

            $unread = ChatMessage::where(function ($q) use ($msisdn) {
                    $q->where('from', $msisdn)->orWhere('to', $msisdn);
                })
                ->where('direction', 'in')
                ->where(function ($q) {
                    $q->whereNull('status')->orWhere('status', '<>', 'read');
                })
                ->count();

            return [
                'peer'         => $msisdn,
                'display_name' => $display,
                'last_text'    => $lastMsg?->text ?? null,
                'last_at'      => $row->last_at ? Carbon::parse($row->last_at, $appTz)->utc()->toIso8601String() : null,
                'unread_count' => $unread,
                'agent_name'   => $agentName,
                'handover'     => $handover,
            ];
        })->values();

        $etagBase = md5($sinceIso.'|'.$rows->toJson(JSON_UNESCAPED_UNICODE));
        $etag     = '"'.$etagBase.'"';

        $normalizeIfNone = function ($v) {
            if ($v === null) return null;
            $v = trim($v);
            if (str_starts_with($v, 'W/')) $v = substr($v, 2);
            return trim($v, '"');
        };

        $clientTag = $normalizeIfNone($request->headers->get('If-None-Match'));
        if ($clientTag !== null && $clientTag === $normalizeIfNone($etag)) {
            return response('', 304)
                ->header('ETag', $etag)
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
                ->header('Vary', 'If-None-Match');
        }

        return response()
            ->json($rows)
            ->header('ETag', $etag)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Vary', 'If-None-Match');
    }

    /* ======================== Helpers ======================== */

    /** Marca mensajes entrantes como leídos (local) al abrir el chat */
    private function markThreadRead(string $msisdn): void
    {
        ChatMessage::where(function ($q) use ($msisdn) {
                $q->where('from', $msisdn)->orWhere('to', $msisdn);
            })
            ->where('direction', 'in')
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '<>', 'read');
            })
            ->update(['status' => 'read']);
    }

    /** Normaliza un mensaje para el front: ISO UTC y media_link */
    private function presentMessageForFront(ChatMessage $m, string $appTz): array
    {
        $isoUtc = '';
        try {
            $isoUtc = Carbon::parse($m->wa_timestamp, $appTz)->utc()->toIso8601String();
        } catch (\Throwable $e) {}

        $mediaLink = null;
        if (in_array($m->type, ['image', 'document'])) {
            $mediaLink = $m->media_id
                ? (\Illuminate\Support\Facades\Route::has('wa.media') ? route('wa.media', $m->media_id) : $m->media_link)
                : $m->media_link;
        }

        return [
            'id'             => $m->id,
            'wamid'          => $m->wamid,
            'from'           => $m->from,
            'to'             => $m->to,
            'direction'      => $m->direction,
            'type'           => $m->type,
            'text'           => $m->text,
            'media_id'       => $m->media_id,
            'media_link'     => $mediaLink,
            'media_filename' => $m->media_filename,
            'wa_timestamp'   => $isoUtc,
            'status'         => $m->status,
        ];
    }

    /** Construye prompt con historial (para que la IA responda con contexto) */
    private function buildHistoryPrompt($history, string $peerMsisdn): string
    {
        $lines = [];
        foreach ($history as $m) {
            $role = $m->direction === 'in' ? 'Cliente' : 'Agente/Empresa';
            $txt  = $m->text ?: ($m->type === 'image' ? '[IMAGEN]' : ($m->type === 'document' ? '[DOCUMENTO]' : '['.$m->type.']'));
            $lines[] = "{$role}: {$txt}";
        }

        $joined = implode("\n", $lines);

        return "Historial reciente (WhatsApp) con el cliente {$peerMsisdn}:\n".
               $joined."\n\n".
               "INSTRUCCIONES: Responde como asistente de MediBuy. Si preguntan por precios/stock, consulta catálogo. ".
               "Si detectas que se requiere humano (cobros, quejas fuertes, negociación, datos sensibles, urgencias), activa handover.\n".
               "Responde breve, claro y con siguiente paso.";
    }
}