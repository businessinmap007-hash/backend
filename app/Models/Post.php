<?php

namespace App\Models;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Post extends Model implements TranslatableContract
{


    use Translatable;
    public $fillable = ['type', 'user_id', 'expire_at', 'is_active'];
    public $translatedAttributes = ['title', 'body'];


    public function comments()
    {
        return $this->hasMany(Comment::class)->where('parent_id', 0);
    }



    public function applies()
    {
        return $this->hasMany(Apply::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function images()
    {
        return $this->morphMany('App\Models\Image', 'imageable');
    }


    public function likes()
    {
        return $this->hasMany(Like::class)->where('like', 1);
    }

    public function dislikes()
    {
        return $this->hasMany(Like::class)->where('like', -1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }



}
