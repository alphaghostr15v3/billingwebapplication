<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_method',
        'transaction_id',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
