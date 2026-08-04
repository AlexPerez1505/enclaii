<?php
namespace App\Http\Controllers;

use App\Models\PushToken;
use App\Services\ExpoPushService;
use Illuminate\Http\Request;

class PushTestController extends Controller
{
    public function sendToMe(Request $request, ExpoPushService $expo)
    {
        $user = $request->user();
        $tokens = PushToken::where('user_id', $user->id)->pluck('token')->toArray();

        if (!$tokens) return response()->json(['ok' => false, 'msg' => 'Sin tokens']);

        $result = $expo->send(
            $tokens,
            'Medibuy',
            'Prueba de notificación ✅',
            ['screen' => 'home']
        );

        return response()->json(['ok' => true, 'result' => $result]);
    }
}