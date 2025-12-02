<?php

namespace App\Models;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Album extends Model implements TranslatableContract
{

    use Translatable;
    public $translatedAttributes = ['title', 'description'];
    protected $fillable = ['image'];

    public function images()
    {
        return $this->morphMany('App\Models\Image', 'imageable');
    }



    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
