<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $connection = 'tenant';
    
    protected $fillable = [
        'name',
        'phone',
        'email',
        'gst_number',
        'address',
        'state',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
