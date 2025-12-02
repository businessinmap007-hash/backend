<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable = [
        'user_id', 'is_active', 'location_lat', 'location_lng'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
