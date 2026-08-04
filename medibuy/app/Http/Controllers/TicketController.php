<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* ===================== VISTAS ===================== */

    public function create()
    {
        $users = User::orderBy('name')->get(['id','name','email','phone']);
        return view('tickets.create', compact('users'));
    }

    public function index(Request $r)
    {
        $me = $r->user();

        $q = Ticket::query()
            ->with([
                'creator:id,name',
                'assignee:id,name',
                'watchers:id,name',
            ])
            ->withCount('comments')
            ->where(function ($qq) use ($me) {
                $qq->where('visibility', 'public')
                   ->orWhere('creator_id', $me->id)
                   ->orWhereHas('watchers', fn($w) => $w->where('users.id', $me->id));
            });

        if ($s = trim((string) $r->input('q'))) {
            $q->where(function ($qq) use ($s) {
                $qq->where('title', 'like', "%{$s}%")
                   ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($status = $r->input('status'))     { $q->where('status', $status); }
        if ($priority = $r->input('priority')) { $q->where('priority', $priority); }
        if ($vis = $r->input('visibility'))    { $q->where('visibility', $vis); }
        if ($type = $r->input('ticket_type'))  { $q->where('ticket_type', $type); }
        if ($area = $r->input('area'))         { $q->where('area', $area); }

        if ($r->boolean('mine'))     { $q->where('creator_id', $me->id); }
        if ($r->boolean('watching')) { $q->whereHas('watchers', fn($w) => $w->where('users.id', $me->id)); }

        $tickets = $q->latest()
            ->paginate((int) $r->input('per_page', 20))
            ->withQueryString();

        $users  = User::orderBy('name')->get(['id','name','email','phone']);
        $people = $users;

        return view('tickets.index', compact('tickets', 'users', 'people'));
    }

    public function show(Request $r, Ticket $ticket)
    {
        $this->abortIfCannotView($r->user(), $ticket);

        $ticket->load([
            'creator:id,name,phone',
            'assignee:id,name,phone',
            'watchers:id,name,phone',
            'attachments',
            'children',
            'children.attachments',
            'comments' => fn($q) => $q->with(['user:id,name', 'attachments'])->latest(),

            // Checklist operativa (tabla)
            'checklistItems.attachments',
            'checklistItems.updater:id,name',
        ]);

        $users = User::orderBy('name')->get(['id','name','email','phone']);
        return view('tickets.show', compact('ticket', 'users'));
    }

    public function edit(Request $r, Ticket $ticket)
    {
        $this->abortIfCannotEdit($r->user(), $ticket);

        $ticket->load([
            'creator:id,name',
            'assignee:id,name',
            'watchers:id,name',
            'attachments',
            'children',
            'children.attachments',

            'checklistItems.attachments',
            'checklistItems.updater:id,name',
        ]);

        $users = User::orderBy('name')->get(['id','name','email','phone']);

        return view('tickets.edit', compact('ticket','users'));
    }

    /**
     * NUEVA VISTA: Supervisor / Creador ve avances (solo lectura)
     */
    public function progress(Request $r, Ticket $ticket)
    {
        $this->abortIfCannotView($r->user(), $ticket);

        // Reglas: SOLO creador puede ver el panel de supervisión
        abort_unless((int)$ticket->creator_id === (int)$r->user()->id, 403, 'Sólo el creador puede ver el avance detallado.');

        $ticket->load([
            'creator:id,name,phone',
            'assignee:id,name,phone',
            'watchers:id,name,phone',
            'checklistItems.attachments',
            'checklistItems.updater:id,name',
            'comments' => fn($q) => $q->with(['user:id,name'])->latest(),
        ]);

        return view('tickets.progress', compact('ticket'));
    }

    /**
     * NUEVA VISTA: Ejecutor / Asignado trabaja checklist
     */
    public function work(Request $r, Ticket $ticket)
    {
        $this->abortIfCannotView($r->user(), $ticket);

        // Reglas: sólo asignado (y opcional watchers) pueden trabajar checklist
        $canWork = (int)$ticket->assignee_id === (int)$r->user()->id;

        // Si quieres permitir watchers también, deja esta línea:
        $canWork = $canWork || ($ticket->visibility === 'shared' && $ticket->watchers()->where('users.id', $r->user()->id)->exists());

        // Si NO quieres watchers trabajando checklist, comenta la línea de arriba y deja sólo assignee.

        abort_unless($canWork, 403, 'Sólo el asignado (y/o watchers) pueden trabajar el checklist.');

        $ticket->load([
            'creator:id,name,phone',
            'assignee:id,name,phone',
            'watchers:id,name,phone',
            'checklistItems.attachments',
            'checklistItems.updater:id,name',
        ]);

        return view('tickets.work', compact('ticket'));
    }

    /* ===================== ACCIONES ===================== */

    public function store(Request $r, WhatsAppService $wa)
    {
        $me = $r->user();

        // Compat: selected -> shared
        if ($r->filled('visibility') && $r->input('visibility') === 'selected') {
            $r->merge(['visibility' => 'shared']);
        }

        // Compat: chips assignees[] -> watchers[]
        if ($r->has('assignees') && !$r->has('watchers')) {
            $r->merge(['watchers' => (array) $r->input('assignees')]);
        }

        // Aceptar checklist desde varias llaves
        $normalizedChecklist = $this->normalizeChecklistFromRequest($r);

        $data = $r->validate([
            'title'        => ['required', 'string', 'max:180'],
            'description'  => ['nullable', 'string'],
            'body'         => ['nullable', 'string'], // compat
            'priority'     => ['required', 'in:low,medium,high,urgent'],
            'status'       => ['nullable', 'in:open,in_progress,resolved,closed'],
            'visibility'   => ['required', 'in:private,shared,public'],
            'assignee_id'  => ['nullable', 'exists:users,id'],
            'watchers'     => ['array'],
            'watchers.*'   => ['integer', 'exists:users,id'],

            'ticket_type'  => ['required', 'in:incidencia,requerimiento,tarea,bug'],
            'area'         => ['nullable', 'string', 'max:50'],
            'parent_id'    => ['nullable', 'exists:tickets,id'],

            'checklist'    => ['nullable', 'array'],

            'attachment_ids'   => ['array'],
            'attachment_ids.*' => ['integer', 'exists:attachments,id'],

            'subtickets'   => ['nullable', 'array'],
        ]);

        // Normaliza "body" -> "description"
        if (!isset($data['description']) && isset($data['body'])) {
            $data['description'] = $data['body'];
        }
        unset($data['body']);

        $data['status']     = $data['status'] ?? 'open';
        $data['creator_id'] = $me->id;

        if (!is_null($normalizedChecklist)) {
            $data['checklist'] = $normalizedChecklist;
        }

        return DB::transaction(function () use ($data, $wa) {

            $watchers      = collect($data['watchers'] ?? [])->unique()->values()->all();
            $attachmentIds = collect($data['attachment_ids'] ?? [])->unique()->values()->all();
            $subtickets    = $data['subtickets'] ?? [];

            unset($data['watchers'], $data['attachment_ids'], $data['subtickets']);

            $ticket = Ticket::create($data);

            // watchers sólo si visibility = shared
            if ($ticket->visibility === 'shared' && count($watchers)) {
                $ticket->watchers()->sync($watchers);
            }

            // anclar adjuntos temporales al ticket
            if (count($attachmentIds)) {
                Attachment::whereIn('id', $attachmentIds)
                    ->whereNull('attachable_type')
                    ->update([
                        'attachable_type' => Ticket::class,
                        'attachable_id'   => $ticket->id,
                    ]);
            }

            // Crear checklistItems si viene checklist (array) y existe relación
            if (!empty($ticket->checklist) && is_array($ticket->checklist) && method_exists($ticket, 'checklistItems')) {
                foreach (array_values($ticket->checklist) as $i => $it) {
                    $ticket->checklistItems()->create([
                        'position'          => $i,
                        'text'              => (string)($it['text'] ?? 'Item '.($i+1)),
                        'required'          => (bool)($it['required'] ?? false),
                        'evidence_required' => (bool)($it['evidence_required'] ?? false),
                        'evidence_types'    => array_values(array_filter((array)($it['evidence_types'] ?? []))),
                        'status'            => 'pending',
                    ]);
                }
            }

            // crear subtickets (opcional)
            foreach ($subtickets as $st) {
                $child = Ticket::create([
                    'parent_id'   => $ticket->id,
                    'creator_id'  => $ticket->creator_id,

                    'assignee_id' => $st['assignee_id'] ?? null,
                    'title'       => $st['title'] ?? 'Subticket',
                    'description' => $st['description'] ?? null,

                    'status'      => 'open',
                    'priority'    => $st['priority'] ?? $ticket->priority,
                    'visibility'  => $ticket->visibility,

                    'ticket_type' => $st['ticket_type'] ?? 'tarea',
                    'area'        => $st['area'] ?? $ticket->area,

                    'checklist'   => $st['checklist'] ?? null,
                ]);

                if ($ticket->visibility === 'shared' && count($watchers)) {
                    $child->watchers()->sync($watchers);
                }
            }

            // Notificaciones (opcional)
            $ticket->load(['assignee:id,phone', 'watchers:id,phone', 'creator:id,phone']);

            if (method_exists($wa, 'notifyMany') && method_exists($wa, 'notifyTicketCreated')) {
                $phones = collect([])
                    ->when($ticket->assignee, fn($c) => $c->push($ticket->assignee->phone))
                    ->merge($ticket->watchers->pluck('phone'))
                    ->push($ticket->creator?->phone)
                    ->filter()->unique()->values()->all();

                $wa->notifyMany(fn($to) => $wa->notifyTicketCreated($to, $ticket), $phones);
            }

            return redirect()
                ->route('tickets.show', $ticket)
                ->with('success', 'Ticket creado correctamente.');
        });
    }

    public function update(Request $r, Ticket $ticket, WhatsAppService $wa)
    {
        $this->abortIfCannotEdit($r->user(), $ticket);

        if ($r->filled('visibility') && $r->input('visibility') === 'selected') {
            $r->merge(['visibility' => 'shared']);
        }

        if ($r->has('assignees') && !$r->has('watchers')) {
            $r->merge(['watchers' => (array) $r->input('assignees')]);
        }

        $normalizedChecklist = $this->normalizeChecklistFromRequest($r);

        $data = $r->validate([
            'title'        => ['sometimes', 'required', 'string', 'max:180'],
            'description'  => ['nullable', 'string'],
            'body'         => ['nullable', 'string'],
            'priority'     => ['sometimes', 'required', 'in:low,medium,high,urgent'],
            'status'       => ['sometimes', 'required', 'in:open,in_progress,resolved,closed'],
            'visibility'   => ['sometimes', 'required', 'in:private,shared,public'],
            'assignee_id'  => ['nullable', 'exists:users,id'],
            'watchers'     => ['array'],
            'watchers.*'   => ['integer', 'exists:users,id'],

            'ticket_type'  => ['sometimes', 'required', 'in:incidencia,requerimiento,tarea,bug'],
            'area'         => ['nullable', 'string', 'max:50'],
            'checklist'    => ['nullable', 'array'],

            'attachment_ids'   => ['array'],
            'attachment_ids.*' => ['integer', 'exists:attachments,id'],
        ]);

        if (!isset($data['description']) && isset($data['body'])) {
            $data['description'] = $data['body'];
        }
        unset($data['body']);

        if (!is_null($normalizedChecklist)) {
            $data['checklist'] = $normalizedChecklist;
        }

        DB::transaction(function () use ($ticket, $data) {

            $watchersProvided = array_key_exists('watchers', $data);
            $watchers = collect($data['watchers'] ?? [])->unique()->values()->all();
            unset($data['watchers']);

            $attachmentIds = collect($data['attachment_ids'] ?? [])->unique()->values()->all();
            unset($data['attachment_ids']);

            $ticket->fill($data)->save();

            if ($watchersProvided) {
                if ($ticket->visibility === 'shared') {
                    $ticket->watchers()->sync($watchers);
                } else {
                    $ticket->watchers()->sync([]);
                }
            }

            if (count($attachmentIds)) {
                Attachment::whereIn('id', $attachmentIds)
                    ->whereNull('attachable_type')
                    ->update([
                        'attachable_type' => Ticket::class,
                        'attachable_id'   => $ticket->id,
                    ]);
            }
        });

        return redirect()->route('tickets.index')->with('success', 'Ticket actualizado.');
    }

    public function changeStatus(Request $r, Ticket $ticket)
    {
        $this->abortIfCannotEdit($r->user(), $ticket);

        $status = $r->validate(['status' => ['required','in:open,in_progress,resolved,closed']])['status'];
        $ticket->update(['status' => $status]);

        return back()->with('success', 'Estado actualizado.');
    }

    public function syncWatchers(Request $r, Ticket $ticket)
    {
        $this->abortIfCannotEdit($r->user(), $ticket);

        $ids = $r->validate([
            'watchers'   => ['array'],
            'watchers.*' => ['integer','exists:users,id']
        ])['watchers'] ?? [];

        if ($ticket->visibility !== 'shared') {
            return back()->with('warning', 'La visibilidad no permite watchers.');
        }

        $ticket->watchers()->sync($ids);

        return back()->with('success', 'Usuarios vinculados al ticket.');
    }

    public function updateWatchers(Request $r, Ticket $ticket)
    {
        return $this->syncWatchers($r, $ticket);
    }

    public function storeComment(Request $r, Ticket $ticket, WhatsAppService $wa)
    {
        $this->abortIfCannotView($r->user(), $ticket);

        $data = $r->validate([
            'body' => ['required', 'string', 'max:4000'],
            'attachment_ids'   => ['array'],
            'attachment_ids.*' => ['integer','exists:attachments,id'],
        ]);

        $attachmentIds = collect($data['attachment_ids'] ?? [])->unique()->values()->all();
        unset($data['attachment_ids']);

        $comment = $ticket->comments()->create([
            'user_id' => $r->user()->id,
            'body'    => $data['body'],
        ]);

        if (count($attachmentIds)) {
            Attachment::whereIn('id', $attachmentIds)
                ->whereNull('attachable_type')
                ->update([
                    'attachable_type' => TicketComment::class,
                    'attachable_id'   => $comment->id,
                ]);
        }

        $ticket->load(['creator:id,phone', 'assignee:id,phone', 'watchers:id,phone']);

        if (method_exists($wa, 'notifyMany') && method_exists($wa, 'notifyTicketComment')) {
            $phones = collect([$ticket->creator?->phone, $ticket->assignee?->phone])
                ->merge($ticket->watchers->pluck('phone'))
                ->reject(fn($p) => !$p)
                ->unique()
                ->values()
                ->all();

            $phones = array_values(array_filter($phones, fn($p) => $p !== ($r->user()->phone ?? null)));

            $wa->notifyMany(
                fn($to) => $wa->notifyTicketComment($to, $ticket->fresh(['creator','assignee','watchers']), $comment->load('user')),
                $phones
            );
        }

        return back()->with('success', 'Comentario agregado.');
    }

    public function destroy(Request $r, Ticket $ticket)
    {
        $this->abortIfCannotEdit($r->user(), $ticket);
        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Ticket eliminado.');
    }

    /* ===================== HELPERS ===================== */

    protected function abortIfCannotView($user, Ticket $ticket): void
    {
        $can = $ticket->visibility === 'public'
            || $ticket->creator_id === $user->id
            || $ticket->watchers()->where('users.id', $user->id)->exists();

        abort_unless($can, 403, 'No puedes ver este ticket.');
    }

    protected function abortIfCannotEdit($user, Ticket $ticket): void
    {
        $can = (int)$ticket->creator_id === (int)$user->id;
        abort_unless($can, 403, 'Sólo el creador puede editar este ticket.');
    }

    private function normalizeChecklistFromRequest(Request $r): ?array
    {
        if ($r->has('checklist') && is_array($r->input('checklist'))) {
            return $this->normalizeChecklistArray($r->input('checklist'));
        }

        $json = $r->input('checklist_json');
        if (is_string($json) && trim($json) !== '') {
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                return $this->normalizeChecklistArray($decoded);
            }
        }

        $ai = $r->input('checklist_ai');
        if (is_string($ai) && trim($ai) !== '') {
            $decoded = json_decode($ai, true);
            if (is_array($decoded)) {
                return $this->normalizeChecklistArray($decoded);
            }
        }

        return null;
    }

    private function normalizeChecklistArray(array $items): array
    {
        $allowed = ['image','video','audio','pdf','document','spreadsheet','link','other'];

        $out = [];
        foreach ($items as $i => $item) {
            if (!is_array($item)) continue;

            $text = trim((string)($item['text'] ?? $item['title'] ?? ''));
            if ($text === '') continue;

            $required = (bool)($item['required'] ?? false);
            $evidenceRequired = (bool)($item['evidence_required'] ?? $item['evidence'] ?? false);

            $types = $item['evidence_types'] ?? [];
            if (!is_array($types)) $types = [];
            $types = array_values(array_unique(array_filter(array_map(function ($t) use ($allowed) {
                $t = strtolower(trim((string)$t));
                return in_array($t, $allowed, true) ? $t : null;
            }, $types))));

            if ($evidenceRequired && empty($types)) {
                $types = ['image'];
            }

            $out[] = [
                'text' => $text,
                'required' => $required,
                'evidence_required' => $evidenceRequired,
                'evidence_types' => $types,
            ];

            if (count($out) >= 20) break;
        }

        return $out;
    }
}