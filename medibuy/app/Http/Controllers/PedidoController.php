<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoEquipo;
use App\Models\PedidoComponente;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PedidoController extends Controller
{
    // Mostrar listado de pedidos
public function index()
{
    // Cargar pedidos con sus equipos y componentes
    $pedidos = Pedido::with(['equipos', 'componentes'])->get();

    // Filtrar solo pedidos que aún tienen componentes pendientes o incompletos
    $pedidosFiltrados = $pedidos->filter(function ($pedido) {
        foreach ($pedido->componentes as $componente) {
            $recibido = \App\Models\RecepcionComponente::where('nombre_componente', $componente->nombre)
                ->where('equipo_id', $componente->equipo_id)
                ->sum('cantidad_recibida');

            if ($recibido < $componente->cantidad_esperada) {
                return true; // Hay faltantes
            }
        }

        return false; // Todo se recibió
    });

    // Convertir fechas programadas a instancia Carbon (opcional si la vista lo requiere)
    $pedidosFiltrados->transform(function ($pedido) {
        $pedido->fecha_programada = Carbon::parse($pedido->fecha_programada);
        return $pedido;
    });

    return view('pedidos.index', ['pedidos' => $pedidosFiltrados]);
}


    // Mostrar formulario para crear pedido
    public function create()
    {
        return view('pedidos.create');
    }

    // Guardar nuevo pedido
public function store(Request $request)
{
    $request->validate([
        'fecha_programada' => 'required|date',
        'creado_por' => 'required|string|max:255',
        'observaciones' => 'nullable|string',
        'equipos' => 'required|array',
        'equipos.*.nombre' => 'required|string|max:255',
        'equipos.*.cantidad' => 'required|integer|min:1',
        'componentes' => 'nullable|array',
        'componentes.*.nombre' => 'required_with:componentes|string|max:255',
        'componentes.*.equipo_id' => 'nullable|integer',
        'componentes.*.cantidad_esperada' => 'required_with:componentes|integer|min:0',
    ]);

    // Crear pedido
    $pedido = Pedido::create([
        'fecha_programada' => $request->fecha_programada,
        'creado_por' => $request->creado_por,
        'observaciones' => $request->observaciones,
    ]);

    // Guardar equipos y mapear índice temporal al id real de la BD
    $equiposMap = []; // índice temporal => id en BD

    foreach ($request->equipos as $idx => $eq) {
        $pedidoEquipo = PedidoEquipo::create([
            'pedido_id' => $pedido->id,
            'nombre' => $eq['nombre'],
            'cantidad' => $eq['cantidad'],
        ]);
        $equiposMap[$idx] = $pedidoEquipo->id; // Guardamos el ID real
    }

    // Guardar componentes, usando el mapa para asignar equipo_id correcto
    if ($request->filled('componentes')) {
        foreach ($request->componentes as $comp) {
            $equipoId = $comp['equipo_id'] ?? null;

            // Convertir índice temporal a ID real si existe
            $equipoRealId = isset($equiposMap[$equipoId]) ? $equiposMap[$equipoId] : null;

            PedidoComponente::create([
                'pedido_id' => $pedido->id,
                'nombre' => $comp['nombre'],
                'equipo_id' => $equipoRealId,
                'cantidad_esperada' => $comp['cantidad_esperada'],
            ]);
        }
    }

    return redirect()->route('pedidos.create')->with('success', 'Pedido creado correctamente.');
}



}
