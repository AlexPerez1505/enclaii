<?php

namespace App\Http\Controllers;

use App\Services\AiChecklistService;
use Illuminate\Http\Request;

class AiTicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * POST /ai/tickets/checklist
     * Body: title, body/description, ticket_type, area
     * Return: { ok: true, checklist: [...] }
     */
    public function checklist(Request $r, AiChecklistService $ai)
    {
        $data = $r->validate([
            'title'       => ['required','string','max:180'],
            'body'        => ['nullable','string','max:6000'],
            'description' => ['nullable','string','max:6000'],
            'ticket_type' => ['required','in:incidencia,requerimiento,tarea,bug'],
            'area'        => ['nullable','string','max:50'],
        ]);

        $title = $data['title'];
        $desc  = (string) ($data['body'] ?? $data['description'] ?? '');
        $type  = $data['ticket_type'];
        $area  = $data['area'] ?? null;

        $checklist = $ai->generateChecklist($title, $desc, $type, $area);

        return response()->json([
            'ok' => true,
            'checklist' => $checklist,
        ]);
    }
}