<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'business_id',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'image',
        'is_active',
        'sort_order',
    ];

    public function business()
    {
        return $this->belongsTo(User::class, 'business_id');
    }

    public function sizes()
    {
        return $this->hasMany(MenuItemSize::class);
    }

    public function extras()
    {
        return $this->hasMany(MenuItemExtra::class);
    }
}
