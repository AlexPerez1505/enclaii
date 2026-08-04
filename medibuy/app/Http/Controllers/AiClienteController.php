<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI;

class AiClienteController extends Controller
{
    public function sugerir(Request $request)
    {
        $cliente = $request->cliente;

        $client = OpenAI::client(env('OPENAI_API_KEY'));

        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Eres un asesor comercial experto en seguimiento de clientes.'
                ],
                [
                    'role' => 'user',
                    'content' => "Genera una recomendación para este cliente: $cliente"
                ]
            ]
        ]);

        return response()->json([
            'respuesta' => $response->choices[0]->message->content
        ]);
    }
}