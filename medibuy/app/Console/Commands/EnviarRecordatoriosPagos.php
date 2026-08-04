<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\PagoFinanciamiento;
use App\Models\PagoRecordatorio;
use App\Models\User;
use App\Mail\PagoPendienteHoyMail;
use App\Mail\PagoPendienteHoyAdminMail;
use App\Services\WhatsAppService;

class EnviarRecordatoriosPagos extends Command
{
    protected $signature   = 'pagos:recordatorios {--dry : Simula sin enviar}';
    protected $description = 'Envía recordatorios por WhatsApp/Email: 7 días antes, día de pago y cada 3 días si está vencido';

    public function handle(WhatsAppService $wa): int
    {
        $dry = (bool)$this->option('dry');
        $tz  = config('app.timezone', 'America/Mexico_City');
        $hoy = Carbon::now($tz)->startOfDay();

        // IMPORTANTE: tu plantilla real y el idioma por defecto
        $tplCliente = env('WHATSAPP_TEMPLATE_PAGO', 'pago_recordatorio_v1'); // <- EXISTE en tu WABA
        $tplAdmin   = env('WHATSAPP_TEMPLATE_PAGO_ADMIN', 'pago_aviso_admin_v1'); // esta ya te aceptó
        $langDef    = env('WHATSAPP_DEFAULT_LANG', 'es_MX');
        $adminMsisdn= env('WHATSAPP_ADMIN_MSISDN');

        $pagos = PagoFinanciamiento::query()
            ->with(['venta.cliente','venta.pagos'])
            ->where('pagado', false)
            ->whereNotNull('fecha_pago')
            ->get();

        $sentCount = 0;

        foreach ($pagos as $pago) {
            $venta   = $pago->venta;
            $cliente = $venta?->cliente;
            if (!$venta || !$cliente) continue;

            $fechaPago = Carbon::parse($pago->fecha_pago, $tz)->startOfDay();
            $clienteNombre = trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''));
            $to = WhatsAppService::normalizeMsisdn($cliente->telefono ?? '');

            $montoFmt = number_format(($pago->monto ?? 0), 2, '.', ',');
            $fechaFmt = $fechaPago->format('d/m/Y');

            // ===== 7 días antes =====
            if ($fechaPago->equalTo($hoy->copy()->addDays(7))) {
                // Cliente
                if ($to && $this->shouldSend($pago->id, 'whatsapp', 'pre7', $tz, 0)) {
                    $sentCount += $this->sendWARecordatorio(
                        dry: $dry,
                        wa: $wa,
                        to: $to,
                        tpl: $tplCliente,
                        langDef: $langDef,
                        headerText: 'Recordatorio de pago', // HEADER requerido por tu template
                        paramsBody: [
                            $clienteNombre,    // {{1}}
                            $venta->id,        // {{2}}
                            $montoFmt,         // {{3}}
                            $fechaFmt,         // {{4}}
                            'Recordatorio (7 días antes)', // {{5}}
                        ],
                        pagoId: $pago->id,
                        stage: 'pre7'
                    );
                }
                // Admin
                if ($adminMsisdn && $this->shouldSend($pago->id, 'whatsapp', 'pre7_admin', $tz, 0)) {
                    $this->sendSimpleTpl($dry, $wa, WhatsAppService::normalizeMsisdn($adminMsisdn), $tplAdmin, $langDef, [
                        $clienteNombre, $venta->id, $montoFmt, $fechaFmt, 'Recordatorio (7 días antes)', 'Se notificó por WA al cliente.',
                    ]);
                    $this->log($pago->id, 'whatsapp', 'pre7_admin');
                    $sentCount++;
                }
            }

            // ===== Mismo día =====
            if ($fechaPago->equalTo($hoy)) {
                // Emails
                if ($this->shouldSend($pago->id, 'email', 'due_client', $tz, 0)) {
                    if (!$dry && !empty($cliente->email)) Mail::to($cliente->email)->queue(new PagoPendienteHoyMail($venta));
                    $this->log($pago->id, 'email', 'due_client');
                    $sentCount++;
                }
                $adminEmails = User::where('role', 'admin')->whereNotNull('email')->pluck('email')->filter();
                foreach ($adminEmails as $adminEmail) {
                    if ($this->shouldSend($pago->id, 'email', 'due_admin', $tz, 0)) {
                        if (!$dry) Mail::to($adminEmail)->queue(new PagoPendienteHoyAdminMail($venta));
                        $this->log($pago->id, 'email', 'due_admin');
                        $sentCount++;
                    }
                }

                // WhatsApp cliente
                if ($to && $this->shouldSend($pago->id, 'whatsapp', 'due', $tz, 0)) {
                    $sentCount += $this->sendWARecordatorio(
                        dry: $dry,
                        wa: $wa,
                        to: $to,
                        tpl: $tplCliente,
                        langDef: $langDef,
                        headerText: 'Vence hoy',
                        paramsBody: [
                            $clienteNombre,
                            $venta->id,
                            $montoFmt,
                            $fechaFmt,
                            'Vence hoy',
                        ],
                        pagoId: $pago->id,
                        stage: 'due'
                    );
                }
                // WhatsApp admin
                if ($adminMsisdn && $this->shouldSend($pago->id, 'whatsapp', 'due_admin', $tz, 0)) {
                    $this->sendSimpleTpl($dry, $wa, WhatsAppService::normalizeMsisdn($adminMsisdn), $tplAdmin, $langDef, [
                        $clienteNombre, $venta->id, $montoFmt, $fechaFmt, 'Vence hoy', 'Se notificó por WA al cliente.',
                    ]);
                    $this->log($pago->id, 'whatsapp', 'due_admin');
                    $sentCount++;
                }
            }

            // ===== Vencido (cada 3 días) =====
            if ($fechaPago->lessThan($hoy)) {
                $diasAtraso = $fechaPago->diffInDays($hoy);
                if ($diasAtraso > 0 && $diasAtraso % 3 === 0) {
                    if ($to && $this->shouldSend($pago->id, 'whatsapp', 'overdue', $tz, 3)) {
                        $sentCount += $this->sendWARecordatorio(
                            dry: $dry,
                            wa: $wa,
                            to: $to,
                            tpl: $tplCliente,
                            langDef: $langDef,
                            headerText: 'Pago vencido',
                            paramsBody: [
                                $clienteNombre,
                                $venta->id,
                                $montoFmt,
                                $fechaFmt,
                                'Pago vencido',
                            ],
                            pagoId: $pago->id,
                            stage: 'overdue'
                        );
                    }
                    if ($adminMsisdn && $this->shouldSend($pago->id, 'whatsapp', 'overdue_admin', $tz, 3)) {
                        $this->sendSimpleTpl($dry, $wa, WhatsAppService::normalizeMsisdn($adminMsisdn), $tplAdmin, $langDef, [
                            $clienteNombre, $venta->id, $montoFmt, $fechaFmt, 'Pago vencido', 'Cliente con atraso; se envió WA.',
                        ]);
                        $this->log($pago->id, 'whatsapp', 'overdue_admin');
                        $sentCount++;
                    }
                }
            }
        }

        $this->info(($dry ? '[DRY] ' : '') . "Mensajes enviados/loggeados: {$sentCount}");
        return Command::SUCCESS;
    }

    /* ================= Helpers ================= */

    private function shouldSend(int $pagoId, string $channel, string $stage, string $tz, int $cooldownDays): bool
    {
        $last = PagoRecordatorio::where('pago_financiamiento_id', $pagoId)
            ->where('channel', $channel)->where('stage', $stage)
            ->orderByDesc('sent_at')->first();

        if (!$last) return true;

        if ($cooldownDays <= 0) return false; // solo una vez si cooldown=0

        return Carbon::parse($last->sent_at, $tz)->lte(Carbon::now($tz)->subDays($cooldownDays));
    }

    private function log(int $pagoId, string $channel, string $stage): void
    {
        PagoRecordatorio::create([
            'pago_financiamiento_id' => $pagoId,
            'channel' => $channel,
            'stage'   => $stage,
            'sent_at' => now(),
        ]);
    }

    /**
     * Envío a cliente usando TU plantilla real (con HEADER TEXT + 5 variables en BODY).
     */
    private function sendWARecordatorio(
        bool $dry,
        WhatsAppService $wa,
        string $to,
        string $tpl,
        string $langDef,
        string $headerText,
        array $paramsBody,
        int $pagoId,
        string $stage
    ): int {
        if ($dry) { $this->log($pagoId, 'whatsapp', $stage); return 1; }

        // 1er intento con idioma por defecto
        $resp = $wa->sendTemplate($to, $tpl, $langDef, $paramsBody, [$headerText]);

        // Si el idioma no existe para esa plantilla, reintenta con un idioma válido
        if ($resp->status() === 400 && data_get($resp->json(), 'error.code') === 132001) {
            $lang = $wa->pickTemplateLanguage($tpl) ?? $langDef;
            $resp = $wa->sendTemplate($to, $tpl, $lang, $paramsBody, [$headerText]);
        }

        $ok = $resp->successful() && data_get($resp->json(), 'messages.0.id');
        if ($ok) {
            $this->log($pagoId, 'whatsapp', $stage);
            return 1;
        }

        Log::warning('WA_FAIL_RECORDATORIO', ['to'=>$to,'tpl'=>$tpl,'resp'=>$resp->json()]);
        return 0;
    }

    /**
     * Envío a admin (tu plantilla admin no requiere header).
     */
    private function sendSimpleTpl(bool $dry, WhatsAppService $wa, string $to, string $tpl, string $lang, array $params): void
    {
        if ($dry) return;

        $resp = $wa->sendTemplate($to, $tpl, $lang, $params);
        if ($resp->status() === 400 && data_get($resp->json(), 'error.code') === 132001) {
            $lang2 = $wa->pickTemplateLanguage($tpl) ?? $lang;
            $wa->sendTemplate($to, $tpl, $lang2, $params);
        }
    }
}
