<?php
// app/Models/ReminderLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderLog extends Model
{
    protected $fillable = ['evento_id','to','when','meta'];
    protected $casts = ['meta' => 'array'];
}
