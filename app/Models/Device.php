<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = ['user_id', 'device', 'device_type'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
