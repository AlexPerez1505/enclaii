<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Ticket;
use App\Models\TicketChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketChecklistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // POST /tickets/{ticket}/checklist/{item}/update
    public function updateItem(Request $r, Ticket $ticket, TicketChecklistItem $item)
    {
        abort_unless($item->ticket_id === $ticket->id, 404);

        $user = $r->user();
        $this->abortIfCannotContribute($user, $ticket);

        $data = $r->validate([
            'status' => ['required','in:pending,in_progress,done,blocked'],
            'what_done' => ['nullable','string','max:4000'],
            'how_done' => ['nullable','string','max:4000'],
            'observations' => ['nullable','string','max:4000'],

            'attachment_ids'   => ['array'],
            'attachment_ids.*' => ['integer','exists:attachments,id'],
        ]);

        $attachmentIds = collect($data['attachment_ids'] ?? [])->unique()->values();
        unset($data['attachment_ids']);

        return DB::transaction(function () use ($ticket, $item, $user, $data, $attachmentIds) {

            // Update item
            $item->fill([
                'status'           => $data['status'],
                'what_done'        => $data['what_done'] ?? null,
                'how_done'         => $data['how_done'] ?? null,
                'observations'     => $data['observations'] ?? null,
                'updated_by'       => $user->id,
                'updated_at_action'=> now(),
            ])->save();

            // Anclar adjuntos temporales al checklist item
            if ($attachmentIds->count()) {
                Attachment::whereIn('id', $attachmentIds->all())
                    ->whereNull('attachable_type')
                    ->update([
                        'attachable_type' => TicketChecklistItem::class,
                        'attachable_id'   => $item->id,
                    ]);
            }

            // Crear comentario automático con bitácora
            $statusLabel = [
                'pending' => 'Pendiente',
                'in_progress' => 'En progreso',
                'done' => 'Hecho',
                'blocked' => 'Bloqueado',
            ][$item->status] ?? $item->status;

            $body = "✅ Actualización de Checklist (Item #".($item->position+1).")\n"
                ."• Item: {$item->text}\n"
                ."• Estado: {$statusLabel}\n";

            if (!empty($item->what_done)) {
                $body .= "\n📌 Qué hice:\n{$item->what_done}\n";
            }
            if (!empty($item->how_done)) {
                $body .= "\n🛠️ Cómo:\n{$item->how_done}\n";
            }
            if (!empty($item->observations)) {
                $body .= "\n📝 Observaciones:\n{$item->observations}\n";
            }

            $comment = $ticket->comments()->create([
                'user_id' => $user->id,
                'body'    => $body,
            ]);

            // Si quieres también ligar evidencias al comentario (opcional):
            // - puedes duplicarlas (no recomendable) o
            // - solo dejarlas en checklist item (recomendado).
            // Aquí las dejamos SOLO en checklist item.

            return back()->with('success', 'Checklist actualizado y registrado en bitácora.');
        });
    }

    protected function abortIfCannotContribute($user, Ticket $ticket): void
    {
        $can = $ticket->creator_id === $user->id
            || ($ticket->assignee_id && (int)$ticket->assignee_id === (int)$user->id)
            || ($ticket->visibility === 'shared' && $ticket->watchers()->where('users.id', $user->id)->exists())
            || ($ticket->visibility === 'public'); // si NO quieres esto, lo quitamos

        abort_unless($can, 403, 'No puedes actualizar checklist en este ticket.');
    }
}