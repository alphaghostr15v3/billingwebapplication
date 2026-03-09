<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'business_name',
        'owner_name',
        'email',
        'phone',
        'gst_number',
        'state',
        'database_name',
        'status',
        'subscription_id',
        'subscription_status',
        'subscription_expires_at',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
