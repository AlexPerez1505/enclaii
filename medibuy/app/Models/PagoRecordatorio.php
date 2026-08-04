<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoRecordatorio extends Model
{
    protected $fillable = ['pago_financiamiento_id','channel','stage','sent_at'];

    public function pago()
    {
        return $this->belongsTo(PagoFinanciamiento::class, 'pago_financiamiento_id');
    }
}
