<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paquete;
use App\Models\Producto;

class PaqueteController extends Controller
{
    /**
     * Listado / panel de paquetes
     */
    public function index()
    {
        $paquetes = Paquete::with([
                'productos' => function ($q) {
                    $q->select('productos.*');
                }
            ])
            ->withCount('productos')
            ->latest()
            ->get();

        return view('paquetes.index', compact('paquetes'));
    }

    /**
     * Productos de un paquete en JSON
     */
    public function getProductosDePaquete($id)
    {
        $paquete = Paquete::with([
                'productos' => function ($q) {
                    $q->select('productos.*');
                }
            ])
            ->findOrFail($id);

        return response()->json([
            'productos' => $paquete->productos
        ]);
    }

    /**
     * Búsqueda de paquetes
     */
    public function search(Request $request)
    {
        $query = $request->input('search');

        $paquetes = Paquete::where('nombre', 'LIKE', "%{$query}%")
            ->with(['productos' => function ($q) {
                $q->select('productos.*');
            }])
            ->get();

        return response()->json($paquetes);
    }

    /**
     * Formulario para crear paquete
     */
    public function create()
    {
        $productos = Producto::orderBy('tipo_equipo')->get();
        return view('paquetes.create', compact('productos'));
    }

    /**
     * Guardar paquete
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'productos'   => 'required|array|min:1',
            'productos.*' => 'required|integer|exists:productos,id',
            'orden'       => 'nullable|array',
            'orden.*'     => 'nullable|integer|min:1|max:9999',
        ]);

        $paquete = Paquete::create([
            'nombre' => $data['nombre'],
        ]);

        $sync = $this->buildSyncArray(
            $data['productos'],
            $data['orden'] ?? null
        );

        $paquete->productos()->sync($sync);

        if ($request->ajax()) {
            $paquete->load(['productos' => function ($q) {
                $q->select('productos.*');
            }]);

            return response()->json([
                'message' => 'Paquete creado correctamente',
                'paquete' => $paquete
            ]);
        }

        return redirect()
            ->route('paquetes.show', $paquete)
            ->with('success', 'Paquete creado');
    }

    /**
     * Ver detalle de un paquete
     */
    public function show(Paquete $paquete)
    {
        $paquete->load(['productos' => function ($q) {
            $q->select('productos.*');
        }]);

        return view('paquetes.show', compact('paquete'));
    }

    /**
     * Formulario de edición de paquete
     */
    public function edit(Paquete $paquete)
    {
        $paquete->load('productos');
        $productos = Producto::orderBy('tipo_equipo')->get();

        return view('paquetes.edit', compact('paquete', 'productos'));
    }

    /**
     * Actualizar paquete
     */
    public function update(Request $request, Paquete $paquete)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'productos'   => 'nullable|array',
            'productos.*' => 'required|integer|exists:productos,id',
            'orden'       => 'nullable|array',
            'orden.*'     => 'nullable|integer|min:1|max:9999',
        ]);

        $paquete->update([
            'nombre' => $data['nombre'],
        ]);

        if (isset($data['productos']) && count($data['productos']) > 0) {
            $sync = $this->buildSyncArray(
                $data['productos'],
                $data['orden'] ?? null
            );

            $paquete->productos()->sync($sync);
        } else {
            $paquete->productos()->detach();
        }

        if ($request->ajax()) {
            $paquete->load(['productos' => function ($q) {
                $q->select('productos.*');
            }]);

            return response()->json([
                'message' => 'Paquete actualizado',
                'paquete' => $paquete
            ]);
        }

        return redirect()
            ->route('paquetes.show', $paquete)
            ->with('success', 'Paquete actualizado');
    }

    /**
     * Eliminar paquete
     */
    public function destroy(Paquete $paquete)
    {
        $paquete->productos()->detach();
        $paquete->delete();

        return redirect()
            ->route('paquetes.create')
            ->with('success', 'Paquete eliminado');
    }

    /**
     * Construye el arreglo para sync() SOLO con orden
     */
    private function buildSyncArray(array $ids, ?array $ordenes = null): array
    {
        $sync = [];

        foreach (array_values($ids) as $i => $id) {
            $order = $i + 1;

            if (is_array($ordenes)) {
                if (array_key_exists($id, $ordenes) && is_numeric($ordenes[$id])) {
                    $order = (int) $ordenes[$id];
                } elseif (array_key_exists($i, $ordenes) && is_numeric($ordenes[$i])) {
                    $order = (int) $ordenes[$i];
                }
            }

            // blindaje extra
            $order = max(1, min(9999, $order));

            $sync[(int) $id] = [
                'orden' => $order,
            ];
        }

        return $sync;
    }
}