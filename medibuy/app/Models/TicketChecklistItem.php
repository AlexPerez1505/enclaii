<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketChecklistItem extends Model
{
    protected $fillable = [
        'ticket_id',
        'position',
        'text',
        'required',
        'evidence_required',
        'evidence_types',
        'status',
        'updated_by',
        'updated_at_action',
        'what_done',
        'how_done',
        'observations',
    ];

    protected $casts = [
        'required'          => 'boolean',
        'evidence_required' => 'boolean',
        'evidence_types'    => 'array',
        'updated_at_action' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Adjuntos ligados a este item
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable')->latest();
    }
}