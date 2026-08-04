<?php
// app/Models/ChatMessage.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'wamid','from','to','direction','type','text',
        'media_link','media_filename','wa_timestamp','status'
    ];
}
