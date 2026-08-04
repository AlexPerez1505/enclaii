<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Pago;

class ReciboController extends Controller
{
   public function formularioVerificacion()
{
    return view('recibos.verificar', ['codigo' => null, 'pagoValido' => null]);
}


    public function verificar(Request $request)
    {
        $codigo = strtoupper($request->input('codigo'));

        $pagoValido = Pago::all()->first(function ($pago) use ($codigo) {
            $hash = strtoupper(substr(sha1($pago->id . $pago->monto . $pago->fecha_pago), 0, 12));
            return $hash === $codigo;
        });

        return view('recibos.verificar', ['pagoValido' => $pagoValido, 'codigo' => $codigo]);
    }
}
