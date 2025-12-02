<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Location extends Model implements TranslatableContract
{


    use Translatable;


    public $translatedAttributes = ['name'];
    protected $fillable = ['name', 'parent_id'];


    public function scopeCountry($query)
    {
        return $query->where('parent_id', 0);
    }

    public  function parent(){
        return $this->belongsTo(Location::class, 'parent_id');
    }


    public function children(){
        return $this->hasMany(Location::class, 'parent_id');
    }


}
