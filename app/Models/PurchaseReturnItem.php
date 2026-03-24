<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'purchase_return_id',
        'product_name',
        'quantity',
        'price',
        'tax_rate',
        'tax_amount',
        'total',
    ];

    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }
}
