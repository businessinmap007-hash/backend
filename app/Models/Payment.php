<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['user_id', 'price', 'payment_type', 'payment_no', 'operation_type', 'operation_id', 'paid_at'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
