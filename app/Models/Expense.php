<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'title',
        'amount',
        'category',
        'date',
        'description',
    ];
}
