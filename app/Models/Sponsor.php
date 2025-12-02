<?php

namespace App\Models;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Sponsor extends Model implements TranslatableContract
{
    use Translatable;
    public $fillable = ['image', 'user_id', 'expire_at','type', 'activated_at', 'price'];
    public $translatedAttributes = ['title','description'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
