<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'return_number',
        'vendor_name',
        'expense_id',
        'date',
        'subtotal',
        'tax_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'total_amount',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }
}
