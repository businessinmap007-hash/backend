<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_order_id',
        'menu_item_id',
        'qty',
        'size',
        'unit_price',
        'total_price'
    ];

    public function order()
    {
        return $this->belongsTo(MenuOrder::class, 'menu_order_id');
    }

    public function item()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }
}
