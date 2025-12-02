<?php

namespace App\Models;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Category extends Model implements TranslatableContract
{


    use Translatable;
    public $translatedAttributes = ['name'];
    protected $fillable = ['parent_id', 'per_month', 'per_year','image','reorder'];

//    public $with = ['children'];


    public function scopeParentCategory($query)
    {
        return $query->where('parent_id', 0);
    }


    public function products(){
        return $this->hasMany(Product::class);
    }


    public function options(){
        return $this->belongsToMany(Option::class,'category_option');
    }



    public  function parent(){
        return $this->belongsTo(Category::class, 'parent_id');
    }


    public function children(){
        return $this->hasMany(Category::class, 'parent_id');
    }




    public function business(){
        return $this->hasMany(User::class);
    }








}
