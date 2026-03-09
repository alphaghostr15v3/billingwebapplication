<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'subtotal',
        'tax_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'discount_amount',
        'total_amount',
        'payment_method',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
