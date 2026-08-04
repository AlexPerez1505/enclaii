<?php
// app/Http/Controllers/WhatsappPromotionController.php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Categoria; // si existe
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Producto;

class WhatsappPromotionController extends Controller
{
    /**
     * Formulario (DIRECT) para enviar con PLANTILLA: promo_img_sin_boton_v1
     * GET /promos/whatsapp/direct
     */
    public function directCreate(Request $request)
    {
        $q         = trim((string)$request->get('q', ''));
        $categoria = $request->get('categoria');

        $clientes = Cliente::with('categoria')
    ->when($q, function ($qr) use ($q) {
        $like = "%{$q}%";

        $qr->where(function ($w) use ($like) {
            $w->where('nombre', 'like', $like)
              ->orWhere('apellido', 'like', $like)
              ->orWhere('telefono', 'like', $like)
              ->orWhere('email', 'like', $like)
              ->orWhere('asesor', 'like', $like)
              ->orWhereHas('categoria', function ($c) use ($like) {
                  $c->where('nombre', 'like', $like);
              });
        });
    })
            ->when($categoria, fn($qr) => $qr->where('categoria_id', $categoria))
            ->orderByRaw("COALESCE(NULLIF(nombre,''),'~') asc")
            ->limit(1000)
            ->get(['id','nombre','apellido','telefono','email','categoria_id','asesor']);

        $categorias = class_exists(Categoria::class)
            ? Categoria::orderBy('nombre')->get(['id','nombre'])
            : collect();
            $productos = Producto::orderBy('tipo_equipo')
    ->orderBy('marca')
    ->get([
        'id',
        'tipo_equipo',
        'subtipo_equipo',
        'marca',
        'modelo',
        'precio',
        'imagen'
    ]);

        return view('promociones.whatsapp-direct', [
    'clientes'   => $clientes,
    'categorias' => $categorias,
    'productos'  => $productos,
    'q'          => $q,
    'categoria'  => $categoria,
]);
    }

    /**
     * Envío (DIRECT) con PLANTILLA: promo_img_sin_boton_v1 (header imagen + body {{1..4}})
     * POST /promos/whatsapp/direct
     */
    public function directSend(Request $request, WhatsAppService $wa)
    {
        // Validación del formulario
        $data = $request->validate([
            'producto'         => ['required','string','max:1500'],   // {{2}}
            'imagen_file'      => ['required','file','mimes:jpg,jpeg,png','max:5120'], // header image (5MB)
            'clientes_ids'     => ['nullable','array'],
            'clientes_ids.*'   => ['integer','exists:clientes,id'],
            'clientes_manual'  => ['nullable','string'],
        ]);

        // 1) Destinatarios (desde BD + manuales)
        $destinatarios = [];

        if (!empty($data['clientes_ids'])) {
            $rows = Cliente::whereIn('id', $data['clientes_ids'])
                ->get(['id','nombre','apellido','telefono']);
            foreach ($rows as $c) {
                $nombre = trim(($c->nombre ?? '').' '.($c->apellido ?? '')) ?: 'Cliente';
                $tel    = WhatsAppService::normalizeMsisdn((string) $c->telefono);
                if ($tel) $destinatarios[] = ['to' => $tel, 'nombre' => $nombre];
            }
        }

        // Manual: "521XXXXXXXXXX|Nombre" ó solo número
        if (!empty($data['clientes_manual'])) {
            $raws = preg_split('/[\r\n,;]+/', (string)$data['clientes_manual']);
            foreach ($raws as $r) {
                $r = trim($r); if ($r === '') continue;
                if (str_contains($r, '|')) {
                    [$num, $nom] = array_map('trim', explode('|', $r, 2));
                    $tel = WhatsAppService::normalizeMsisdn($num);
                    if ($tel) $destinatarios[] = ['to' => $tel, 'nombre' => $nom ?: 'Cliente'];
                } else {
                    $tel = WhatsAppService::normalizeMsisdn($r);
                    if ($tel) $destinatarios[] = ['to' => $tel, 'nombre' => 'Cliente'];
                }
            }
        }

        // Normalizar + dedupe por teléfono
        $seen = [];
        $destinatarios = array_values(array_filter($destinatarios, function ($d) use (&$seen) {
            $k = preg_replace('/\D+/', '', $d['to']);
            if ($k === '') return false;
            if (isset($seen[$k])) return false;
            $seen[$k] = true; return true;
        }));

        // Evitar auto-envío al remitente
        $senderE164 = preg_replace('/\D+/', '', (string) config('services.whatsapp.phone_e164', env('WHATSAPP_PHONE_NUMBER', '')));
        if ($senderE164 !== '') {
            $destinatarios = array_values(array_filter($destinatarios, function ($d) use ($senderE164) {
                return preg_replace('/\D+/', '', $d['to']) !== $senderE164;
            }));
        }

        if (empty($destinatarios)) {
            return back()->with('wa_info', 'No seleccionaste destinatarios.')->withInput();
        }

        // 2) Subir imagen 1 sola vez -> media_id
        try {
            $upload = $wa->uploadMedia($request->file('imagen_file'));
        } catch (\Throwable $e) {
            \Log::error('WA_MEDIA_UPLOAD_EXCEPTION', ['e' => $e->getMessage()]);
            return back()->with('wa_info', 'No se pudo preparar la subida de imagen.')->withInput();
        }

        $mediaId = data_get($upload->json(), 'id');
        if (!$upload->successful() || !$mediaId) {
            \Log::warning('WA_MEDIA_UPLOAD_FAIL', ['resp' => $upload->json()]);
            return back()->with('wa_info', 'No se pudo subir la imagen a WhatsApp.')->withInput();
        }

        // 3) Enviar por TEMPLATE con header IMAGEN
        $cfg     = config('services.whatsapp');
        $token   = (string) ($cfg['token'] ?? '');
        $version = (string) ($cfg['version'] ?? 'v21.0');
        $phoneId = (string) ($cfg['phone_id'] ?? '');
        $endpoint= "https://graph.facebook.com/{$version}/{$phoneId}/messages";

        // Nombre de plantilla y fallback de idiomas desde .env
        $plantilla = (string) env('WA_PROMO_TPL_IMG', 'promo_img_sin_boton_v1');
        $langPrefs = array_values(array_unique(array_filter(array_map('trim', explode(',', (string)
            env('WA_PROMO_LANG', 'es_MX,es,es_ES,en_US')
        )))));

        $producto  = (string) $data['producto'];

        $delayMs = (int) env('WA_PROMO_DELAY_MS', 0);
        $ok = []; $fail = [];

        foreach (array_chunk($destinatarios, 100) as $bloque) {
            foreach ($bloque as $d) {
                $components = [
                    [
                        'type' => 'header',
                        'parameters' => [[
                            'type' => 'image',
                            'image' => [ 'id' => $mediaId ],
                        ]],
                    ],
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $d['nombre']],   // {{1}}
                            ['type' => 'text', 'text' => $producto],      // {{2}}
                        ],
                    ],
                ];

                $sentOk = false; $lastJson = null; $lastCode = null; $usedLang = null;

                foreach ($langPrefs as $langTry) {
                    $payload = [
                        'messaging_product' => 'whatsapp',
                        'to'       => $d['to'],
                        'type'     => 'template',
                        'template' => [
                            'name'       => $plantilla,
                            'language'   => ['code' => $langTry],
                            'components' => $components,
                        ],
                    ];

                    try {
                        $resp = Http::withToken($token)->acceptJson()->asJson()->timeout(30)->post($endpoint, $payload);
                        $json = $resp->json();
                    } catch (\Throwable $e) {
                        $json = ['error' => ['message' => $e->getMessage(), 'code' => 'HTTP_EXC']];
                    }

                    $lastJson = $json;
                    $lastCode = (string) data_get($json, 'error.code');
                    $wamid    = data_get($json, 'messages.0.id');

                    if ($resp instanceof \Illuminate\Http\Client\Response && $resp->successful() && $wamid) {
                        $ok[] = ['to' => $d['to'], 'wamid' => $wamid, 'lang' => $langTry];
                        \Log::info('WA_OK_TPL_IMG', ['to' => $d['to'], 'wamid' => $wamid, 'lang' => $langTry]);
                        $usedLang = $langTry;
                        $sentOk = true;
                        break;
                    }

                    // Si la traducción no existe, probamos con el siguiente idioma
                    if ($lastCode === '132001') {
                        continue;
                    } else {
                        // Otros errores: salimos del ciclo de idiomas
                        break;
                    }
                }

                if (!$sentOk) {
                    $fail[] = [
                        'to'     => $d['to'],
                        'code'   => $lastCode,
                        'detail' => data_get($lastJson,'error.message') ?: data_get($lastJson,'error.error_data.details'),
                        'raw'    => $lastJson,
                    ];
                    \Log::warning('WA_FAIL_TPL_IMG', ['to' => $d['to'], 'resp' => $lastJson]);
                }

                if ($delayMs > 0) usleep($delayMs * 1000);
            }
        }

        $msg = count($ok) . " enviado(s), " . count($fail) . " falló(aron).";
        if (count($fail)) {
            return back()->with('wa_info', $msg)->with('wa_fail', $fail)->withInput();
        }

        return back()->with('wa_success', $msg . ' (plantilla: '.$plantilla.')');
    }

    /* ---- Alias opcionales para compatibilidad con "promo-img" ---- */
    public function promoImgTplCreate(Request $request){ return $this->directCreate($request); }
    public function promoImgTplSend(Request $request, WhatsAppService $wa){ return $this->directSend($request, $wa); }
}