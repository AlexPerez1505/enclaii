<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SolicitudMaterial;
use App\Notifications\NuevaSolicitudRecibida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsAppService;

class SolicitudMaterialController extends Controller
{
    /**
     * Lista de solicitudes del usuario autenticado.
     */
    public function index()
    {
        $solicitudes = SolicitudMaterial::where('user_id', auth()->id())->latest()->get();
        return view('solicitudes.index', compact('solicitudes'));
    }

    /**
     * Form para crear solicitud.
     */
    public function create()
    {
        $categorias = [
            'Papelería','Limpieza','Herramientas','Administración','Ventas','Logística y Envíos',
            'Almacén','Mantenimiento de Equipo Médico','Servicio Técnico','Sistemas / TI','Compras',
            'Marketing','Seguridad e Higiene','Mobiliario de Oficina','Uniformes','Publicidad',
            'Capacitación','Combustible y Transporte','Reparaciones Generales','Hojalatería y Pintura','Otros'
        ];

        return view('solicitudes.create', compact('categorias'));
    }

    /**
     * Guarda la solicitud y notifica (correo + plantilla WhatsApp a múltiples admins).
     */
    public function store(Request $request)
    {
        Log::info('STORE_START', ['user'=>auth()->id(), 'payload'=>$request->only('categoria','material','cantidad')]);

        $request->validate([
            'categoria'     => 'required|string',
            'material'      => 'required|string',
            'cantidad'      => 'required|integer|min:1',
            'justificacion' => 'nullable|string',
        ]);

        $solicitud = SolicitudMaterial::create([
            'user_id'       => auth()->id(),
            'categoria'     => $request->categoria,
            'material'      => $request->material,
            'cantidad'      => $request->cantidad,
            'justificacion' => $request->justificacion,
        ]);

        Log::info('STORE_CREATED', ['solicitud_id'=>$solicitud->id]);

        // Email al admin (como ya tenías)
        $admin = User::where('email', 'al222111300@gmail.com')->first(); // ajusta al correo real
        if ($admin) {
            $admin->notify(new NuevaSolicitudRecibida($solicitud));
            Log::info('MAIL_ADMIN_SENT', ['admin'=>$admin->id ?? null]);
        } else {
            Log::warning('MAIL_ADMIN_NOT_FOUND');
        }

        // WhatsApp: enviar plantilla a TODOS los números de admin
        $adminPhones = $this->adminPhones();
        if (empty($adminPhones)) {
            Log::warning('WA_ADMIN_PHONES_MISSING');
        } else {
            $detalle = trim(
                $solicitud->categoria
                .' · '.$solicitud->material
                .' · '.$solicitud->cantidad.' pzas'
                .($solicitud->justificacion ? ' · Comentario: '.mb_strimwidth($solicitud->justificacion, 0, 120, '…') : '')
            );

            foreach ($adminPhones as $adminPhone) {
                try {
                    Log::info('WA_ADMIN_SEND_ATTEMPT', ['to'=>$adminPhone, 'id'=>$solicitud->id]);

                    $wa = app(WhatsAppService::class);

                    if (env('WA_BTN_SUFFIX_ADD_SLASH', false)) {
                        // Si la base del botón en la plantilla NO tiene "/" al final,
                        // mandamos el sufijo con "/" + id
                        $res = $wa->sendTemplateText(
                            $adminPhone,
                            'alerta_solicitud_admin_svc_v1',
                            'es_MX',
                            [$solicitud->id, (auth()->user()->name ?? 'N/D'), $detalle, now()->format('d/m/Y H:i')],
                            ['/' . $solicitud->id]
                        );
                    } else {
                        // Si la base del botón SÍ termina con "/", usamos tu atajo
                        $res = $wa->sendAdminAlertTemplate(
                            $adminPhone,
                            $solicitud->id,
                            auth()->user()->name ?? 'N/D',
                            $detalle,
                            now()->format('d/m/Y H:i')
                        );
                    }

                    if (method_exists($res, 'failed') ? $res->failed() : (method_exists($res, 'successful') && !$res->successful())) {
                        Log::warning('WA_ADMIN_TPL_FAILED', [
                            'to'     => $adminPhone,
                            'id'     => $solicitud->id,
                            'status' => $res->status(),
                            'resp'   => $res->json(),
                        ]);
                    } else {
                        Log::info('WA_ADMIN_SENT_OK', ['to'=>$adminPhone, 'resp'=>$res->json()]);
                    }
                } catch (\Throwable $e) {
                    Log::error('WA_ADMIN_SENT_FAIL', [
                        'to'    => $adminPhone,
                        'id'    => $solicitud->id,
                        'ex'    => $e->getMessage()
                    ]);
                }
            }
        }

        // (Opcional) WhatsApp texto al USUARIO confirmando recepción
        if ($userPhone = $this->userPhone($solicitud)) {
            $msgUser = "✅ Recibimos tu solicitud #{$solicitud->id}
Categoría: {$solicitud->categoria}
Material: {$solicitud->material}
Cantidad: {$solicitud->cantidad}
Te avisaremos cualquier cambio. Gracias 🙌";
            $this->sendWA($userPhone, $msgUser);
        }

        Log::info('STORE_END', ['id'=>$solicitud->id]);

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud enviada correctamente.');
    }

    // ===== VISTA ADMIN =====

    public function pendientes()
    {
        $solicitudes = SolicitudMaterial::whereIn('estado', ['Pendiente', 'En Planta'])->latest()->get();
        return view('solicitudes.admin', compact('solicitudes'));
    }

    public function marcarComoEnPlanta(SolicitudMaterial $solicitud)
    {
        $solicitud->update(['estado' => 'En Planta']);

        if ($phone = $this->userPhone($solicitud)) {
            $this->sendWA($phone, "📦 Tu solicitud #{$solicitud->id} ya está *En Planta*. Puedes pasar a recogerla.");
        }

        return back()->with('success', 'Material marcado como disponible en planta.');
    }

    public function entregar(SolicitudMaterial $solicitud)
    {
        $solicitud->update([
            'estado'        => 'Entregado',
            'fecha_entrega' => now(),
            'entregado_por' => auth()->id(),
        ]);

        if ($phone = $this->userPhone($solicitud)) {
            $this->sendWA($phone, "✅ Entregamos tu solicitud #{$solicitud->id} el ".now()->format('d/m/Y H:i').". ¡Gracias!");
        }

        return back()->with('success', 'Solicitud marcada como entregada.');
    }

    public function rechazar(Request $request, SolicitudMaterial $solicitud)
    {
        $request->validate([
            'motivo_rechazo' => 'required|string|max:255',
        ]);

        $solicitud->update([
            'estado'         => 'Rechazada',
            'motivo_rechazo' => $request->motivo_rechazo,
        ]);

        if ($phone = $this->userPhone($solicitud)) {
            $this->sendWA($phone, "❌ Tu solicitud #{$solicitud->id} fue *rechazada*.\nMotivo: {$solicitud->motivo_rechazo}");
        }

        return redirect()->back()->with('success', 'Solicitud rechazada con éxito.');
    }

    public function misSolicitudesAjax()
    {
        $solicitudes = auth()->user()->solicitudes()->latest()->get();
        return view('partials.solicitudes', compact('solicitudes'));
    }

    public function marcarEnPlanta(SolicitudMaterial $solicitud)
    {
        $solicitud->estado = 'En Planta';
        $solicitud->save();

        if ($phone = $this->userPhone($solicitud)) {
            $this->sendWA($phone, "📦 Tu solicitud #{$solicitud->id} ya está en *Planta*.");
        }

        return response()->json(['success' => true]);
    }

    public function listado()
    {
        $solicitudes = SolicitudMaterial::whereIn('estado', ['Pendiente', 'En Planta'])->latest()->get();
        return view('partials.listado', compact('solicitudes'));
    }

    // ============================================================
    // ===================== Helpers privados =====================
    // ============================================================

    /**
     * Lee múltiples MSISDNs del admin desde .env (separados por coma, espacio, ; o |),
     * los normaliza a E.164 (MX: 521 + 10 dígitos) y devuelve array único.
     */
    private function adminPhones(): array
    {
        $raw = trim((string) env('WHATSAPP_ADMIN_MSISDN', ''));
        if ($raw === '') return [];

        $parts = preg_split('/[,\s;|]+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
        $out = [];
        foreach ($parts as $p) {
            $num = WhatsAppService::normalizeMsisdn($p);
            if ($num) $out[] = $num;
        }
        return array_values(array_unique($out));
    }

    /**
     * (Compat) Número único desde .env; no se usa en el envío múltiple pero lo dejamos por si acaso.
     */
    private function adminPhone(): ?string
    {
        $arr = $this->adminPhones();
        return $arr[0] ?? null;
    }

    /**
     * Teléfono del dueño de la solicitud, normalizado.
     */
    private function userPhone(SolicitudMaterial $solicitud): ?string
    {
        $raw = optional($solicitud->user)->telefono ?? null; // Ajusta si tu campo es distinto
        return $raw ? WhatsAppService::normalizeMsisdn($raw) : null;
    }

    /**
     * Enviar texto libre a WhatsApp (para usuario).
     */
    private function sendWA(string $to, string $text): void
    {
        try {
            $res = app(WhatsAppService::class)->sendText($to, $text);
            if (method_exists($res, 'failed') ? $res->failed() : (method_exists($res, 'successful') && !$res->successful())) {
                Log::warning('WA_SEND_TEXT_FAILED', [
                    'to'     => $to,
                    'status' => $res->status(),
                    'resp'   => $res->json(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('WA_SEND_TEXT_EXCEPTION', ['to' => $to, 'error' => $e->getMessage()]);
        }
    }
}
