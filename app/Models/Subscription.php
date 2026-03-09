<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'plan_name',
        'price',
        'duration_days',
    ];
}
