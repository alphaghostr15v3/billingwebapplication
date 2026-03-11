<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'name',
        'sku',
        'hsn_number',
        'category_id',
        'price',
        'gst_percentage',
        'stock_quantity',
        'low_stock_limit',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
