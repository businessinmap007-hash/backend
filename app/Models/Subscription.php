<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['is_active', 'category_id','duration', 'coupon_id','price', 'finished_at','code_type'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
