<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PromoController extends Controller
{
    /**
     * Formulario: lista clientes (con filtros) + subida de imagen y frase.
     */
    public function create(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $soloConTelefono = $request->boolean('solo_con_telefono', true);

        $base = Cliente::query();

        if ($q !== '') {
            $base->where(function ($w) use ($q) {
                $w->where('nombre', 'like', "%{$q}%")
                  ->orWhere('apellido', 'like', "%{$q}%")
                  ->orWhere('telefono', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($soloConTelefono) {
            // Teléfonos con al menos 10 dígitos en cualquier formato
            $base->whereNotNull('telefono')
                 ->whereRaw("REGEXP_REPLACE(telefono, '[^0-9]', '') REGEXP '^[0-9]{10,}$'");
        }

        $totalFiltrados = (clone $base)->count();

        $clientes = $base
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->paginate(25)
            ->withQueryString();

        return view('promos.promo_todo', [
            'clientes'         => $clientes,
            'q'                => $q,
            'soloConTelefono'  => $soloConTelefono,
            'totalFiltrados'   => $totalFiltrados,
        ]);
    }

    /**
     * Envío:
     * - Sube header_image -> media_id (Meta)
     * - Envía plantilla promo_todo a seleccionados o a todos los filtrados
     */
    public function send(Request $request, WhatsAppService $wa)
    {
        $data = $request->validate([
            // ✅ CUALQUIER IMAGEN + SIN LIMITE EN VALIDACIÓN LARAVEL
            // (ojo: tu servidor puede bloquear por php.ini y WhatsApp puede rechazar tamaños enormes)
            'header_image'       => ['required', 'file', 'mimetypes:image/*'],

            'frase'              => ['required', 'string', 'max:500'],
            'mode'               => ['required', 'in:selected,all_filtered'],

            // cuando mode=selected (ids[])
            'ids'                => ['array'],
            'ids.*'              => ['integer'],

            // filtros para mode=all_filtered
            'q'                  => ['nullable', 'string', 'max:255'],
            'solo_con_telefono'  => ['nullable', 'in:0,1'],
        ]);

        // 1) Subir imagen a /media
        try {
            $upload = $wa->uploadMedia($request->file('header_image'));
        } catch (\Throwable $e) {
            Log::error('WA_MEDIA_UPLOAD_EXCEPTION', ['e' => $e->getMessage()]);
            return back()->withErrors(['header_image' => 'No se pudo subir la imagen (excepción).'])->withInput();
        }

        if (!$upload || !$upload->ok()) {
            Log::error('WA_MEDIA_UPLOAD_FAIL', ['resp' => $upload?->json()]);
            return back()->withErrors(['header_image' => 'No se pudo subir la imagen al API de WhatsApp.'])->withInput();
        }

        $mediaId = data_get($upload->json(), 'id');
        if (!$mediaId) {
            Log::error('WA_MEDIA_UPLOAD_NO_ID', ['resp' => $upload->json()]);
            return back()->withErrors(['header_image' => 'WhatsApp no regresó media_id.'])->withInput();
        }

        // 2) Resolver destinatarios
        $rows = collect();

        if ($data['mode'] === 'selected') {
            $ids = collect($data['ids'] ?? [])->filter()->unique();
            if ($ids->isEmpty()) {
                return back()->withErrors(['ids' => 'Selecciona al menos un cliente.'])->withInput();
            }
            $rows = Cliente::whereIn('id', $ids)->get();
        } else { // all_filtered
            $q = trim((string) ($data['q'] ?? ''));
            $soloConTel = ($data['solo_con_telefono'] ?? '1') === '1';

            $base = Cliente::query();

            if ($q !== '') {
                $base->where(function ($w) use ($q) {
                    $w->where('nombre', 'like', "%{$q}%")
                      ->orWhere('apellido', 'like', "%{$q}%")
                      ->orWhere('telefono', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            }

            if ($soloConTel) {
                $base->whereNotNull('telefono')
                     ->whereRaw("REGEXP_REPLACE(telefono, '[^0-9]', '') REGEXP '^[0-9]{10,}$'");
            }

            $rows = $base->get();

            if ($rows->isEmpty()) {
                return back()->withErrors(['ids' => 'No hay clientes en el filtro actual.'])->withInput();
            }
        }

        // 3) Envío
        $ok = 0; $fail = 0; $sinTelefono = 0;
        $results = [];

        foreach ($rows as $c) {
            /** @var \App\Models\Cliente $c */
            $nombre = trim(implode(' ', array_filter([$c->nombre ?? null, $c->apellido ?? null]))) ?: 'Cliente';
            $rawTel = (string) ($c->telefono ?? '');

            // Normaliza a E.164 (MX)
            $to = WhatsAppService::normalizeMsisdn($rawTel);

            if (!preg_match('/^\d{11,14}$/', $to)) {
                $sinTelefono++;
                $results[] = [
                    'to'     => $rawTel,
                    'nombre' => $nombre,
                    'ok'     => false,
                    'status' => 0,
                    'wamid'  => null,
                    'resp'   => ['error' => 'Teléfono inválido/insuficiente'],
                ];
                continue;
            }

            $res = $wa->sendPromoTodo($to, $mediaId, $nombre, $data['frase']);

            $ok   += $res->successful() ? 1 : 0;
            $fail += $res->successful() ? 0 : 1;

            $json  = $res->json();
            $wamid = data_get($json, 'messages.0.id');

            $results[] = [
                'to'     => $to,
                'nombre' => $nombre,
                'ok'     => $res->successful(),
                'status' => $res->status(),
                'wamid'  => $wamid,
                'resp'   => $json,
            ];
        }

        Log::info('PROMO_TODO_RESULTS', [
            'total'       => count($results),
            'ok'          => $ok,
            'fail'        => $fail,
            'sinTelefono' => $sinTelefono,
        ]);

        $msg = "Envíos OK: {$ok} | Fallidos: {$fail}";
        if ($sinTelefono > 0) $msg .= " | Sin teléfono válido: {$sinTelefono}";

        return back()->with([
            'status'  => $msg,
            'results' => $results,
        ]);
    }
}
