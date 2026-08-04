<?php

namespace App\Http\Controllers;

use App\Models\PagoFinanciamiento;
use App\Models\PagoRecordatorio;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

// Mails
use App\Mail\PagoPendienteHoyMail;
use App\Mail\PagoPendienteHoyAdminMail;

// WhatsApp
use App\Services\WhatsAppService;

class NotificacionPagoController extends Controller
{
    /**
     * Reenvía notificación manual (correo + WhatsApp).
     * Usa las plantillas:
     *  - Cliente: env('WHATSAPP_TEMPLATE_PAGO', 'pago_recordatorio_v1')
     *      HEADER (1): Título corto (ej. "Vence hoy")
     *      BODY (5):   {{1}} Nombre | {{2}} #Venta | {{3}} $Monto | {{4}} Fecha | {{5}} Etiqueta
     *
     *  - Admin: env('WHATSAPP_TEMPLATE_PAGO_ADMIN', 'pago_aviso_admin_v1')
     *      BODY (6):   {{1}} Nombre | {{2}} #Venta | {{3}} $Monto | {{4}} Fecha | {{5}} Etapa | {{6}} Nota
     */
    public function reenviar(PagoFinanciamiento $pago, WhatsAppService $wa)
    {
        Log::info("Reenviando notificación MANUAL para PagoFinanciamiento ID {$pago->id}");

        // Cargar relaciones necesarias
        $pago->load('venta.cliente');
        $venta   = $pago->venta;
        $cliente = $venta?->cliente;

        if (!$venta || !$cliente) {
            Log::warning('No hay venta/cliente asociado al pago.');
            return response()->json(['error' => 'Venta/cliente no disponible'], 422);
        }

        // ========= Datos comunes
        $clienteNombre = trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''));
        $ventaId       = (int) $venta->id;
        $montoFmt      = number_format((float)($pago->monto ?? 0), 2, '.', ',');
        $fechaFmt      = $pago->fecha_pago
                        ? Carbon::parse($pago->fecha_pago)->format('d/m/Y')
                        : Carbon::now()->format('d/m/Y');
        $etiquetaHoy   = 'Vence hoy';

        // ========= EMAILS
        try {
            if (!empty($cliente->email)) {
                Mail::to($cliente->email)->send(new PagoPendienteHoyMail($venta));
                Log::info("MAIL_CLIENTE_OK", ['venta_id' => $ventaId, 'to' => $cliente->email]);
            }
        } catch (\Throwable $e) {
            Log::error("MAIL_CLIENTE_FAIL", ['venta_id' => $ventaId, 'e' => $e->getMessage()]);
        }

        try {
            $adminEmails = User::where('role', 'admin')->whereNotNull('email')->pluck('email')->filter();
            foreach ($adminEmails as $adminEmail) {
                Mail::to($adminEmail)->send(new PagoPendienteHoyAdminMail($venta));
                Log::info("MAIL_ADMIN_OK", ['venta_id' => $ventaId, 'to' => $adminEmail]);
            }
            if ($adminEmails->isEmpty()) {
                Log::warning('No hay correos de admins configurados.');
            }
        } catch (\Throwable $e) {
            Log::error("MAIL_ADMIN_FAIL", ['venta_id' => $ventaId, 'e' => $e->getMessage()]);
        }

        // ========= WHATSAPP – nombres de plantilla e idioma
        $tplCliente = env('WHATSAPP_TEMPLATE_PAGO', 'pago_recordatorio_v1');   // HEADER 1 + BODY 5
        $tplAdmin   = env('WHATSAPP_TEMPLATE_PAGO_ADMIN', 'pago_aviso_admin_v1'); // BODY 6

        $langCliente = $wa->pickTemplateLanguage($tplCliente) ?? env('WHATSAPP_DEFAULT_LANG', 'es_MX');
        $langAdmin   = $wa->pickTemplateLanguage($tplAdmin)   ?? $langCliente;

        // ========= WHATSAPP – Cliente (HEADER + BODY)
        try {
            $toCliente = WhatsAppService::normalizeMsisdn($cliente->telefono ?? '');
            if ($toCliente && preg_match('/^52\d{10,11}$/', $toCliente)) {
                $headerCliente = [$etiquetaHoy]; // 1 variable en HEADER
                $bodyCliente   = [
                    $clienteNombre, // {{1}}
                    $ventaId,       // {{2}}
                    $montoFmt,      // {{3}}
                    $fechaFmt,      // {{4}}
                    $etiquetaHoy,   // {{5}}
                ];

                $respC = $wa->sendTemplate($toCliente, $tplCliente, $langCliente, $bodyCliente, $headerCliente);
                Log::info('WA_REENVIAR_MANUAL_CLIENTE', ['to' => $toCliente, 'resp' => $respC->json()]);

                $this->logRecordatorio($pago->id, 'whatsapp', 'due'); // 👈 channel = whatsapp
            } else {
                Log::warning('WA_CLIENTE_MSISDN_INVALIDO', ['raw' => $cliente->telefono ?? null, 'normalized' => $toCliente]);
            }
        } catch (\Throwable $e) {
            Log::error('WA_CLIENTE_FAIL', ['e' => $e->getMessage()]);
        }

        // ========= WHATSAPP – Admin (solo BODY)
        try {
            $adminMsisdnRaw = env('WHATSAPP_ADMIN_MSISDN');
            if ($adminMsisdnRaw) {
                $toAdmin = WhatsAppService::normalizeMsisdn($adminMsisdnRaw);
                if ($toAdmin && preg_match('/^52\d{10,11}$/', $toAdmin)) {

                    // Orden BODY admin (6):
                    // {{1}} Nombre | {{2}} #Venta | {{3}} $Monto | {{4}} Fecha | {{5}} Etapa | {{6}} Nota
                    $bodyAdmin = [
                        $clienteNombre,                     // {{1}}
                        $ventaId,                           // {{2}}
                        $montoFmt,                          // {{3}}
                        $fechaFmt,                          // {{4}}
                        $etiquetaHoy,                       // {{5}}
                        'Se notificó al cliente por WhatsApp.', // {{6}}
                    ];

                    // No mandamos HEADER para admin (para evitar mismatch si no existe header en la plantilla)
                    $respA = $wa->sendTemplate($toAdmin, $tplAdmin, $langAdmin, $bodyAdmin /* , $header = [] */);
                    Log::info('WA_REENVIAR_MANUAL_ADMIN', ['to' => $toAdmin, 'resp' => $respA->json()]);

                    $this->logRecordatorio($pago->id, 'whatsapp', 'due'); // 👈 channel = whatsapp
                } else {
                    Log::warning('WA_ADMIN_MSISDN_INVALIDO', ['raw' => $adminMsisdnRaw, 'normalized' => $toAdmin]);
                }
            } else {
                Log::warning('WA_ADMIN_MSISDN_NO_CONFIGURADO (.env WHATSAPP_ADMIN_MSISDN)');
            }
        } catch (\Throwable $e) {
            Log::error('WA_ADMIN_FAIL', ['e' => $e->getMessage()]);
        }

        // Marca la bandera antigua si existe la columna
        try {
            if (Schema::hasColumn('pagos_financiamiento', 'notificado')) {
                $pago->notificado = true;
                $pago->save();
            }
        } catch (\Throwable $e) {
            Log::warning('FLAG_NOTIFICADO_FAIL', ['e' => $e->getMessage()]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Guarda el registro de control en pago_recordatorios.
     * channel: 'email' | 'whatsapp'
     * stage:   'pre7' | 'due' | 'overdue'
     */
    private function logRecordatorio(int $pagoId, string $channel, string $stage): void
    {
        try {
            PagoRecordatorio::create([
                'pago_financiamiento_id' => $pagoId,
                'channel' => $channel, // <- 'whatsapp' o 'email'
                'stage'   => $stage,
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('PAGO_RECORDATORIO_LOG_FAIL', [
                'pago_id' => $pagoId,
                'channel' => $channel,
                'stage'   => $stage,
                'e'       => $e->getMessage(),
            ]);
        }
    }
}
