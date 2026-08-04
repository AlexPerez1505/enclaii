<?php
// app/Http/Controllers/WhatsappMediaController.php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappMediaController extends Controller
{
    public function show(string $mediaId)
    {
        $token   = config('services.whatsapp.token');
        $version = config('services.whatsapp.version', 'v21.0');

        // 1) obtener url y mime del media_id
        $meta = Http::withToken($token)
            ->acceptJson()
            ->get("https://graph.facebook.com/{$version}/{$mediaId}", [
                'fields' => 'url,mime_type,sha256,file_size',
            ]);

        if (!$meta->ok()) {
            Log::warning('WA_MEDIA_META_FAIL', ['id'=>$mediaId,'resp'=>$meta->json()]);
            abort(404);
        }

        $url  = $meta->json('url');
        $mime = $meta->json('mime_type') ?: 'application/octet-stream';

        // 2) descargar binario (la URL ya viene firmada)
        $bin = Http::withOptions(['stream' => true])->get($url);
        if (!$bin->ok()) {
            Log::warning('WA_MEDIA_FETCH_FAIL', ['id'=>$mediaId,'status'=>$bin->status()]);
            abort(404);
        }

        return response()->stream(function () use ($bin) {
            while (!feof($bin->toPsrResponse()->getBody())) {
                echo $bin->toPsrResponse()->getBody()->read(8192);
            }
        }, 200, [
            'Content-Type'        => $mime,
            'Cache-Control'       => 'private, max-age=86400',
            'Content-Disposition' => 'inline',
        ]);
    }
}
