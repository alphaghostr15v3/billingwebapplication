<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'invoice_id',
        'product_id',
        'product_name',
        'quantity',
        'price',
        'gst_percentage',
        'total',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
