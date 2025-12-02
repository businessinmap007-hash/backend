<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Address extends Model implements TranslatableContract
{

    use Translatable;
    public $translatedAttributes = ['state', 'street'];
    protected $fillable = ['location_id', 'zip_code', 'latitude', 'longitude'];


    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function details()
    {
        return optional($this->location->parent)->name . ' - ' . optional($this->location)->name . ' - ' . $this->street . ' - ' . $this->state;
    }
}
