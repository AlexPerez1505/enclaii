<?php
// app/Http/Controllers/InvKitController.php
namespace App\Http\Controllers;
use App\Models\InvKit;

class InvKitController extends Controller
{
  public function index() {
    return InvKit::where('is_activa', true)
      ->orderBy('nombre')->get(['id','nombre','slug','aplica_a','version']);
  }

  public function show($slug) {
    $kit = InvKit::where('slug',$slug)->with(['componentes' => fn($q)=>$q->select('inv_componentes_cat.id','inv_componentes_cat.nombre')])->firstOrFail();
    return response()->json([
      'kit' => ['id'=>$kit->id,'nombre'=>$kit->nombre,'slug'=>$kit->slug],
      'componentes' => $kit->componentes->map(fn($c)=>[
        'id'=>$c->id,'nombre'=>$c->nombre,
        'requerido'=>$c->pivot->requerido,
        'cantidad_default'=>$c->pivot->cantidad_default,
      ]),
    ]);
  }
}
