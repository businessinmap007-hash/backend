<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuCart extends Model
{
    protected $fillable = [
        'user_id',
        'business_id',
        'menu_item_id',
        'size_id',
        'qty',
        'unit_price',
        'total_price',
        'options',
        'notes'
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function business() {
        return $this->belongsTo(User::class, 'business_id');
    }

    public function item() {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function size() {
        return $this->belongsTo(MenuItemSize::class, 'size_id');
    }
}
