<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItemExtra extends Model
{
    protected $fillable = [
        'menu_item_id',
        'name_ar',
        'name_en',
        'price',
        'is_active',
    ];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
