<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'creator_id',
        'assignee_id',
        'title',
        'description',
        'status',
        'priority',
        'visibility',
        'resolved_at',

        // NUEVOS
        'ticket_type',
        'area',
        'parent_id',
        'checklist',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'checklist'   => 'array',
    ];

    /* ==== Relaciones ==== */

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    // Pivot: ticket_user (ticket_id, user_id)
    public function watchers()
    {
        return $this->belongsToMany(User::class, 'ticket_user')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    // Adjuntos del ticket (polimórfico)
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable')->latest();
    }

    // Subtickets
    public function parent()
    {
        return $this->belongsTo(Ticket::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Ticket::class, 'parent_id')->latest();
    }
// Checklist items (tabla)
public function checklistItems()
{
    return $this->hasMany(\App\Models\TicketChecklistItem::class)->orderBy('position');
}
    /* ==== Scopes ==== */

    public function scopeVisibleFor($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('visibility', 'public')
              ->orWhere('creator_id', $user->id)
              ->orWhereHas('watchers', fn($w) => $w->where('users.id', $user->id));
        });
    }
}