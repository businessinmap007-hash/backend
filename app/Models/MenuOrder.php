<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_id',
        'cart_id',
        'total_price',
        'payment_method',
        'address',
        'notes',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(MenuOrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function business()
    {
        return $this->belongsTo(User::class, 'business_id');
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
