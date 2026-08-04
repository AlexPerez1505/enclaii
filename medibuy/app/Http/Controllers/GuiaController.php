<?php

namespace App\Http\Controllers;

use App\Models\Guia;
use Illuminate\Http\Request;

class GuiaController extends Controller
{
    public function create() {
        return view('guias'); // SPA
    }

    public function store(Request $request) {
        $request->validate([
            'numero_rastreo'  => 'required|max:14|unique:guias,numero_rastreo',
            'peso'            => 'required|numeric',
            'fecha_recepcion' => 'required|date',
        ], [
            'numero_rastreo.unique' => 'Esta guía ya ha sido registrada.',
        ]);

        $guia = Guia::create($request->only(['numero_rastreo','peso','fecha_recepcion']));

        if ($request->expectsJson()) {
            return response()->json([
                'ok'      => true,
                'message' => 'Guía registrada exitosamente.',
                'guia'    => $guia,
            ]);
        }

        return redirect()->route('guias.create')->with('success', 'Guía registrada exitosamente.');
    }

    // Guías sin entrega (para cards) con fecha formateada
    public function disponibles(Request $request) {
        $rows = Guia::whereDoesntHave('entrega')
            ->orderBy('created_at','desc')
            ->limit(30)
            ->get(['id','numero_rastreo','peso','fecha_recepcion'])
            ->map(function($g){
                return [
                    'id'              => $g->id,
                    'numero_rastreo'  => $g->numero_rastreo,
                    'peso'            => (float) $g->peso,
                    'fecha_recepcion' => $g->fecha_recepcion
                        ? $g->fecha_recepcion->format('Y-m-d')   // evita "Invalid Date" en JS
                        : null,
                ];
            });

        return response()->json($rows);
    }

    /**
     * Buscador paginado para el autosuggest.
     * GET /guias/search?q=1234&per_page=6&solo_disponibles=1
     * (también acepta ?search= por compatibilidad)
     */
    public function getGuias(Request $request) {
        $needle = trim((string)($request->get('q', $request->get('search', ''))));

        // 👇 FIX: el front (entrega.blade.php) formatea el input con espacios
        // cada 4 dígitos (ej. "8187 5280 8334"), pero numero_rastreo se
        // guarda sin espacios en la BD ("818752808334"). Sin esta limpieza,
        // el LIKE nunca hace match y la guía nunca aparece en el buscador.
        $needle = preg_replace('/\D/', '', $needle);

        $per    = max(1, min((int)$request->get('per_page', 8), 50));
        $solo   = filter_var($request->get('solo_disponibles', true), FILTER_VALIDATE_BOOLEAN);

        $query = Guia::query();
        if ($solo) {
            $query->whereDoesntHave('entrega');
        }
        if ($needle !== '') {
            $query->where('numero_rastreo', 'like', "%{$needle}%");
        }

        $page = $query->orderBy('created_at','desc')
            ->select('id','numero_rastreo','peso')
            ->paginate($per);

        return response()->json($page); // { data: [...], current_page, last_page, ... }
    }

    public function resumen(Request $request)
    {
        // Base: guías PENDIENTES (elige el filtro que aplica a tu esquema)
        $base = Guia::query()
            // Variante A: si tienes relación EntregaGuia:
            ->whereDoesntHave('entrega')
            // Variante B (alternativa): si tienes columna boolean 'entregado'
            //->where('entregado', false)
            // Variante C (alternativa): si usas fecha de entrega:
            //->whereNull('fecha_entrega')
            ;

        // Total de pendientes
        $total = (clone $base)->count();

        // Conteo por peso (2 decimales) — usa COALESCE a 0
        $byWeight = (clone $base)
            ->selectRaw('ROUND(COALESCE(peso,0),2) as kg, COUNT(*) as c')
            ->groupBy('kg')
            ->orderBy('kg')
            ->get();

        return response()->json([
            'ok'      => true,
            'total'   => $total,
            'byWeight'=> $byWeight,   // [{kg: "0.00", c: 3}, {kg:"5.00", c:4}, ...]
        ]);
    }
}