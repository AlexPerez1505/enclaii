<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuenta;
use PDF;

class CuentaController extends Controller
{
    public function create()
    {
        return view('cuentas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'lugar'      => 'required|string|min:2',
            'camioneta'  => 'required|string|min:1', // 👈 nuevo
            'casetas'    => 'required|numeric',
            'gasolina'   => 'required|numeric',
            'viaticos'   => 'required|numeric',
            'adicional'  => 'nullable|numeric',
            'descripcion'=> 'nullable|string',
            'total'      => 'required|numeric',
        ]);

        Cuenta::create([
            'lugar'       => $request->lugar,
            'camioneta'   => $request->camioneta, // 👈 nuevo
            'casetas'     => $request->casetas,
            'gasolina'    => $request->gasolina,
            'viaticos'    => $request->viaticos,
            'adicional'   => $request->adicional ?? 0,
            'descripcion' => $request->descripcion ?? '',
            'total'       => $request->total,
        ]);

        return redirect()->route('cuentas.index')->with('success', 'Cuenta registrada correctamente.');
    }

    public function index()
    {
        $cuentas = Cuenta::orderBy('created_at', 'desc')->get();
        return view('cuentas.index', compact('cuentas'));
    }

    public function destroy($id)
    {
        $cuenta = Cuenta::findOrFail($id);
        $cuenta->delete();

        return redirect()->route('cuentas.index')->with('success', 'Cuenta eliminada correctamente.');
    }

    public function exportarPDF()
    {
        $cuentas = Cuenta::orderBy('created_at', 'desc')->get();
        $pdf = PDF::loadView('cuentas.pdf', compact('cuentas'));
        return $pdf->download('cuentas.pdf');
    }

    public function edit($id)
    {
        $cuenta = Cuenta::findOrFail($id);
        return view('cuentas.edit', compact('cuenta'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lugar'       => 'required|string|min:2',
            'camioneta'   => 'required|string|min:1', // 👈 nuevo
            'casetas'     => 'required|numeric|min:0',
            'gasolina'    => 'required|numeric|min:0',
            'viaticos'    => 'required|numeric|min:0',
            'adicional'   => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $cuenta = Cuenta::findOrFail($id);

        $cuenta->lugar       = $request->lugar;
        $cuenta->camioneta   = $request->camioneta; // 👈 nuevo
        $cuenta->casetas     = $request->casetas;
        $cuenta->gasolina    = $request->gasolina;
        $cuenta->viaticos    = $request->viaticos;
        $cuenta->adicional   = $request->adicional ?? 0;
        $cuenta->descripcion = $request->descripcion ?? '';

        // Mantengo tu lógica actual del total
        $cuenta->total = $request->casetas
                        + $request->gasolina
                        + $request->viaticos
                        + ($request->adicional ?? 0)
                        + 500;

        $cuenta->save();

        return redirect()->route('cuentas.index')->with('success', 'Cuenta actualizada correctamente.');
    }
}
