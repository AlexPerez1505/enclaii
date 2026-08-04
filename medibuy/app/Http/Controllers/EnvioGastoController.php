<?php

namespace App\Http\Controllers;

use App\Models\EnvioGasto;
use Illuminate\Http\Request;

class EnvioGastoController extends Controller
{
    // GET /envios-gastos
    public function index(Request $request)
    {
        $q          = trim((string)$request->get('q', ''));
        $sucursal   = $request->get('sucursal');
        $from       = $request->get('from'); // YYYY-MM-DD
        $to         = $request->get('to');   // YYYY-MM-DD

        $query = EnvioGasto::query();

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $like = "%{$q}%";
                $w->where('referencia', 'like', $like)
                  ->orWhere('destino', 'like', $like)
                  ->orWhere('transportista', 'like', $like)
                  ->orWhere('sucursal', 'like', $like)
                  ->orWhere('notas', 'like', $like);
            });
        }

        if (!empty($sucursal)) {
            $query->where('sucursal', $sucursal);
        }

        if (!empty($from)) {
            $query->whereDate('fecha_envio', '>=', $from);
        }
        if (!empty($to)) {
            $query->whereDate('fecha_envio', '<=', $to);
        }

        $query->orderBy('fecha_envio', 'desc')->orderBy('id', 'desc');

        $envios   = $query->paginate(15)->withQueryString();
        $totalGasto = (clone $query)->sum('costo_mxn');
        $conteo   = (clone $query)->count();

        return view('envios_gastos.index', compact('envios','totalGasto','conteo','q','sucursal','from','to'));
    }

    // GET /envios-gastos/create
    public function create()
    {
        return view('envios_gastos.create');
    }

    // POST /envios-gastos
    public function store(Request $request)
    {
        $data = $request->validate([
            'referencia'     => ['nullable','string','max:255'],
            'sucursal'       => ['required','string','max:120'],
            'destino'        => ['nullable','string','max:180'],
            'transportista'  => ['nullable','string','max:120'],

            'alto_cm'        => ['nullable','numeric','min:0'],
            'largo_cm'       => ['nullable','numeric','min:0'],
            'ancho_cm'       => ['nullable','numeric','min:0'],
            'peso_kg'        => ['nullable','numeric','min:0'],

            'costo_mxn'      => ['required','numeric','min:0'],
            'fecha_envio'    => ['required','date'],

            'notas'          => ['nullable','string'],
        ]);

        // Calcular peso volumétrico si hay dimensiones (divisor estándar 5000)
        $vol = null;
        $fact = null;
        if ($this->tieneDimensiones($data)) {
            $divisor = 5000; // cm³/kg - puedes ajustar o guardar como config
            $alto  = (float)($data['alto_cm']   ?? 0);
            $largo = (float)($data['largo_cm']  ?? 0);
            $ancho = (float)($data['ancho_cm']  ?? 0);
            $vol   = ($alto * $largo * $ancho) / $divisor;
        }
        $real = isset($data['peso_kg']) ? (float)$data['peso_kg'] : 0;
        if (!is_null($vol)) {
            $fact = max($real, $vol);
        } else {
            $fact = $real ?: null;
        }

        $data['peso_volumetrico_kg'] = $vol ? round($vol, 2) : null;
        $data['peso_facturable_kg']  = $fact ? round($fact, 2) : null;

        EnvioGasto::create($data);

        return redirect()
            ->route('envios-gastos.index')
            ->with('ok', 'Envío guardado correctamente.');
    }

    private function tieneDimensiones(array $d): bool
    {
        return isset($d['alto_cm'],$d['largo_cm'],$d['ancho_cm'])
            && $d['alto_cm']  !== null && $d['alto_cm']  !== ''
            && $d['largo_cm'] !== null && $d['largo_cm'] !== ''
            && $d['ancho_cm'] !== null && $d['ancho_cm'] !== '';
    }
}
