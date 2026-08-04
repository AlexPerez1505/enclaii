<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class TicketCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Crea comentario por AJAX y devuelve JSON */
    public function store(Request $r, Ticket $ticket, WhatsAppService $wa)
    {
        $this->abortIfCannotView($r->user(), $ticket);

        $data = $r->validate([
            'body' => ['required','string','max:4000'],
            'attachment_ids'   => ['array'],
            'attachment_ids.*' => ['integer','exists:attachments,id'],
        ]);

        $attachmentIds = collect($data['attachment_ids'] ?? [])->unique()->values()->all();
        unset($data['attachment_ids']);

        $comment = $ticket->comments()->create([
            'user_id' => $r->user()->id,
            'body'    => $data['body'],
        ])->load('user:id,name');

        // Anclar adjuntos temporales al comentario
        if ($attachmentIds) {
            Attachment::whereIn('id', $attachmentIds)
                ->whereNull('attachable_type')
                ->update([
                    'attachable_type' => TicketComment::class,
                    'attachable_id'   => $comment->id,
                ]);
        }

        $comment->load('attachments');

        // Notificar (igual que en el controlador principal)
        $phones = collect([$ticket->creator?->phone, $ticket->assignee?->phone])
            ->merge($ticket->watchers()->pluck('phone'))
            ->reject(fn($p) => !$p || $p === $r->user()->phone)
            ->unique()
            ->values()
            ->all();

        if (method_exists($wa, 'notifyMany') && method_exists($wa, 'notifyTicketComment')) {
            $wa->notifyMany(
                fn($to) => $wa->notifyTicketComment($to, $ticket->fresh(['creator','assignee','watchers']), $comment),
                $phones
            );
        }

        return response()->json(['ok'=>true, 'comment'=>$comment]);
    }

    /** Borra comentario propio o si eres admin/creador del ticket */
    public function destroy(Request $r, Ticket $ticket, TicketComment $comment)
    {
        $user = $r->user();

        $can = $comment->user_id === $user->id
            || ($user->role ?? null) === 'admin'
            || $ticket->creator_id === $user->id;

        abort_unless($can, 403, 'No puedes eliminar este comentario.');

        $comment->delete();

        return response()->json(['ok'=>true]);
    }

    /* --- helper --- */
    protected function abortIfCannotView($user, Ticket $ticket): void
    {
        $can = $ticket->visibility === 'public'
            || $ticket->creator_id === $user->id
            || $ticket->watchers()->where('users.id', $user->id)->exists();

        abort_unless($can, 403, 'No puedes ver este ticket.');
    }
}