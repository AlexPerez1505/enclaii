<?php
namespace App\Http\Controllers;
use App\Models\Componente;
use App\Models\Pedido;
use App\Models\Recepcion;
use App\Models\RecepcionComponente;
use Illuminate\Http\Request;
use App\Models\PedidoComponente;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class RecepcionController extends Controller
{
    // Mostrar formulario
public function createDesdePedido(Pedido $pedido)
{
    // Cargar equipos y componentes
    $pedido->load(['equipos', 'componentes']);

    // Recorremos los componentes para calcular lo recibido previamente
    foreach ($pedido->componentes as $componente) {
        $recibido = \App\Models\RecepcionComponente::where('nombre_componente', $componente->nombre)
            ->where('equipo_id', $componente->equipo_id)
            ->whereHas('recepcion', function ($q) use ($pedido) {
                $q->where('pedido_id', $pedido->id);
            })
            ->sum('cantidad_recibida');

        // Guardamos la cantidad ya recibida como propiedad virtual
        $componente->cantidad_ya_recibida = $recibido;
    }

    // Filtrar equipos para que solo contengan componentes no completamente recibidos
    $pedido->equipos = $pedido->equipos->filter(function ($equipo) use ($pedido) {
        $componentes = $pedido->componentes->where('equipo_id', $equipo->id);
        foreach ($componentes as $comp) {
            if ($comp->cantidad_ya_recibida < $comp->cantidad_esperada) {
                return true;
            }
        }
        return false; // Ocultar equipo si todos sus componentes ya fueron recibidos
    });

    return view('recepciones.create_desde_pedido', compact('pedido'));
}


    // Guardar recepción desde pedido
    public function storeDesdePedido(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'fecha' => 'required|date',
            'recibido_por' => 'required|string|max:255',
            'componentes' => 'required|array',
            'observaciones' => 'nullable|string',
        ]);

        // Validar componentes antes de guardar
        foreach ($request->componentes as $grupo) {
            foreach ($grupo as $comp) {
                if (isset($comp['recibido']) && $comp['recibido'] == '1') {
                    if (empty($comp['equipo_id']) || !is_numeric($comp['equipo_id'])) {
                        return back()->withErrors(['error' => 'Falta el ID del equipo para un componente recibido.'])->withInput();
                    }
                    if (!isset($comp['cantidad_recibida']) || $comp['cantidad_recibida'] < 0) {
                        return back()->withErrors(['error' => 'Cantidad recibida inválida en un componente.'])->withInput();
                    }
                }
            }
        }

        // Crear la recepción
        $recepcion = Recepcion::create([
            'pedido_id' => $request->pedido_id,
            'fecha' => $request->fecha,
            'recibido_por' => $request->recibido_por,
            'observaciones' => $request->observaciones,
        ]);

        // Crear componentes recibidos
        foreach ($request->componentes as $grupo) {
            foreach ($grupo as $comp) {
                if (!isset($comp['recibido']) || $comp['recibido'] != '1') continue;

                RecepcionComponente::create([
                    'recepcion_id' => $recepcion->id,
                    'equipo_id' => $comp['equipo_id'],
                    'nombre_equipo' => $comp['equipo'] ?? 'No especificado',
                    'nombre_componente' => $comp['nombre'] ?? 'Sin nombre',
                    'cantidad_recibida' => $comp['cantidad_recibida'] ?? 0,
                    'observaciones' => $comp['observacion'] ?? null,
                ]);
            }
        }

        return redirect()->route('recepciones.index')->with('success', 'Recepción registrada correctamente.');
    }
public function index()
{
    $recepciones = \App\Models\Recepcion::with('componentes')->latest()->paginate(10);
    return view('recepciones.index', compact('recepciones'));
}
public function showHistorialGlobal()
{
    // Recepciones más recientes primero
    $recepciones = \App\Models\Recepcion::with(['componentes', 'pedido'])
        ->orderBy('fecha', 'desc')
        ->get();

    // Agrupar lo recibido por nombre + equipo y sumar cantidades
    $componentesRecibidos = \App\Models\RecepcionComponente::select('nombre_componente', 'equipo_id', \DB::raw('SUM(cantidad_recibida) as total_recibida'))
        ->groupBy('nombre_componente', 'equipo_id')
        ->get()
        ->keyBy(function ($item) {
            return $item->nombre_componente . '|' . $item->equipo_id;
        });

    // Clasificar los componentes esperados
    $componentesEsperados = \App\Models\PedidoComponente::with('pedido')
        ->orderBy('id', 'desc')
        ->get();

    $componentesPendientes = [];
    $componentesParciales = [];

    foreach ($componentesEsperados as $componente) {
        $key = $componente->nombre . '|' . $componente->equipo_id;

        if (!isset($componentesRecibidos[$key])) {
            $componentesPendientes[] = $componente;
        } elseif ($componentesRecibidos[$key]->total_recibida < $componente->cantidad_esperada) {
            $componente->cantidad_recibida = $componentesRecibidos[$key]->total_recibida;
            $componentesParciales[] = $componente;
        }
    }

    // Obtener todos los pedidos disponibles para el filtro en la vista
    $pedidosDisponibles = \App\Models\Pedido::orderBy('id', 'desc')->get();

    return view('recepciones.timeline-global', compact(
        'recepciones',
        'componentesPendientes',
        'componentesParciales',
        'pedidosDisponibles'
    ));
}

public function exportarPDF(Request $request)
{
    $pedidoId = $request->input('pedido_id');

    // Cargar recepciones con sus componentes
    $recepciones = Recepcion::with(['componentes', 'pedido'])
        ->when($pedidoId, function ($query) use ($pedidoId) {
            $query->whereHas('pedido', function ($q) use ($pedidoId) {
                $q->where('id', $pedidoId);
            });
        })
        ->orderBy('fecha', 'desc')
        ->get();

    // Agrupar componentes recibidos
    $componentesRecibidos = RecepcionComponente::select(
            'nombre_componente', 'equipo_id',
            \DB::raw('SUM(cantidad_recibida) as total_recibida')
        )
        ->groupBy('nombre_componente', 'equipo_id')
        ->get()
        ->keyBy(fn($i) => $i->nombre_componente . '|' . $i->equipo_id);

    // Cargar los componentes esperados con pedido
    $componentesEsperados = PedidoComponente::with('pedido')
        ->when($pedidoId, function ($query) use ($pedidoId) {
            $query->where('pedido_id', $pedidoId);
        })
        ->orderBy('id', 'desc')
        ->get();

    $componentesPendientes = [];
    $componentesParciales = [];

    foreach ($componentesEsperados as $c) {
        $key = $c->nombre . '|' . $c->equipo_id;

        if (!isset($componentesRecibidos[$key])) {
            $componentesPendientes[] = $c;
        } elseif ($componentesRecibidos[$key]->total_recibida < $c->cantidad_esperada) {
            $totalRecibido = $componentesRecibidos[$key]->total_recibida;

            // Obtener fechas de recepción desde todas las recepciones
            $fechasRecepcion = [];
            foreach ($recepciones as $r) {
                foreach ($r->componentes as $rc) {
                    if (
                        $rc->nombre_componente === $c->nombre &&
                        $rc->equipo_id == $c->equipo_id &&
                        $r->pedido_id == $c->pedido_id
                    ) {
                        $fechasRecepcion[] = \Carbon\Carbon::parse($r->fecha)->format('d/m/Y H:i');
                    }
                }
            }

            // Agregar detalles adicionales para el PDF
            $c->cantidad_recibida = $totalRecibido;
            $c->fecha_programada = optional($c->pedido)->fecha_programada
                ? \Carbon\Carbon::parse($c->pedido->fecha_programada)->format('d/m/Y')
                : 'No disponible';
            $c->fechas_recepcion = $fechasRecepcion;

            $componentesParciales[] = $c;
        }
    }

    $pdf = PDF::loadView('recepciones.timeline-pdf', compact(
        'recepciones',
        'componentesPendientes',
        'componentesParciales'
    ));

    return $pdf->download('reporte_recepciones.pdf');
}


}
