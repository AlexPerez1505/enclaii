<?php

namespace App\Http\Controllers;

use App\Models\EntregaGuia;
use App\Models\Guia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EntregaGuiaController extends Controller
{
    /**
     * Formulario de captura de entrega (firma, imagen, etc).
     */
    public function create()
    {
        return view('entrega'); // resources/views/entrega.blade.php
    }

    /**
     * Vista del listado (tabla custom AJAX).
     */
    public function index()
    {
        return view('entregas'); // resources/views/entregas.blade.php
    }

    /**
     * === JSON para tabla custom (no DataTables) ===
     * GET /entregas/list?q=&page=1&per_page=12
     *
     * - Busca en: contenido, numero_serie, destinatario, observaciones y
     *   numero_rastreo (ignorando espacios)
     * - Orden: fecha_entrega DESC
     */
// EntregaGuiaController.php
        public function list(Request $request)
        {
            $search  = trim((string)$request->get('q', ''));
            $perPage = max(5, min((int)$request->get('per_page', 12), 50));

            $q = EntregaGuia::with('guia')
                ->when($search, function ($qq) use ($search) {
                    $qq->where(function ($w) use ($search) {
                        $w->where('contenido', 'like', "%{$search}%")
                        ->orWhere('numero_serie', 'like', "%{$search}%")
                        ->orWhere('destinatario', 'like', "%{$search}%")
                        ->orWhere('observaciones', 'like', "%{$search}%")
                        ->orWhereHas('guia', fn($g) => $g->where('numero_rastreo', 'like', "%{$search}%"));
                    });
                })
                ->orderByRaw('COALESCE(fecha_entrega, created_at) DESC')
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $data = collect($q->items())->map(function ($e) {
            return [
                'rastreo'       => $e->guia?->numero_rastreo ?? '—',
                'contenido'     => $e->contenido,             // <- lo enviamos al front
                'serie'         => $e->numero_serie,
                'destinatario'  => $e->destinatario,
                'observaciones' => $e->observaciones,
                'fecha'         => optional($e->fecha_entrega)->format('Y-m-d H:i'),
                'usuario'       => $e->entregado_por,
                'imagen_url'    => $e->imagen ? \Storage::url($e->imagen) : null,
                'estado'        => 'Entregado',
            ];
            });

            return response()->json([
                'data' => $data,
                'meta' => [
                    'page'      => $q->currentPage(),
                    'last_page' => $q->lastPage(),
                    'total'     => $q->total(),
                    'per_page'  => $q->perPage(),
                ],
            ]);
        }
    /**
     * === JSON para DataTables (compat) ===
     * GET /entregas/data
     * Devuelve arreglo simple bajo 'data'.
     */
    public function data(Request $request)
    {
        $rows = EntregaGuia::with('guia')
            ->orderBy('fecha_entrega', 'desc')
            ->get()
            ->map(function (EntregaGuia $e) {
                return [
                    'id'            => $e->id,
                    'guia'          => $e->guia?->numero_rastreo ?? '—',
                    'contenido'     => $e->contenido,
                    'numero_serie'  => $e->numero_serie,
                    'destinatario'  => $e->destinatario,
                    'observaciones' => $e->observaciones,
                    'fecha_entrega' => optional($e->fecha_entrega)->format('d/m/Y H:i'),
                    'entregado_por' => $e->entregado_por,
                    'imagen'        => $e->imagen ? Storage::url($e->imagen) : null,
                ];
            });

        return response()->json(['data' => $rows]);
    }

    /**
     * Registrar la entrega de una guía.
     */
public function store(Request $request)
{
    $request->validate([
        'guia_id'         => ['required', Rule::exists('guias', 'id')],
        'contenido'       => 'required|string',
        'numero_serie'    => 'required|array|min:1',
        'numero_serie.*'  => 'required|string',
        'destinatario'    => 'required|string',
        'observaciones'   => 'nullable|string',
        'firmaDigital'    => 'required|string', // dataURL (base64) del canvas
        'imagen'          => 'nullable|mimes:jpg,jpeg,png,gif,webp,heic,heif,bmp,tiff,svg|max:8192',
    ]);

        // Evitar entregas duplicadas de la misma guía
        $yaEntregada = EntregaGuia::where('guia_id', $request->guia_id)->exists();
        if ($yaEntregada) {
            $msg = 'Esta guía ya tiene una entrega registrada.';
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['guia_id' => $msg])->withInput();
        }

        // Subir imagen (opcional)
        $imagenPath = null;
        if ($request->hasFile('imagen')) {
            $imagenPath = $request->file('imagen')->store('imagenes', 'public');
        }
    $entrega = EntregaGuia::create([
        'guia_id'       => $request->guia_id,
        'entregado_por' => Auth::user()->name,
        'fecha_entrega' => now(),
        'contenido'     => $request->contenido,
        'numero_serie'  => implode(', ', $request->numero_serie), // <-- cambio clave
        'observaciones' => $request->observaciones,
        'destinatario'  => $request->destinatario,
        'firmaDigital'  => $request->firmaDigital,
        'imagen'        => $imagenPath,
    ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok'      => true,
                'message' => 'Guía entregada.',
                'entrega' => $entrega->load('guia'),
            ]);
        }

        return redirect()->route('entrega.create')->with('success', 'Guía entregada.');
    }
}
