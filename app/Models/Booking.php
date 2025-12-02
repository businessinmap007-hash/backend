<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'business_id', 'service_id',
        'date', 'time', 'status', 'notes'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function business() { return $this->belongsTo(User::class); }
    public function service() { return $this->belongsTo(Service::class); }
   

   
}
