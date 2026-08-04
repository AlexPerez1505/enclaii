<?php
// app/Models/ChatFlow.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatFlow extends Model
{
    protected $fillable = ['from', 'step', 'context'];
    protected $casts = [
        'context' => 'array',
    ];
}
