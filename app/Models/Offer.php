<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Offer extends Model implements TranslatableContract
{

    use Translatable;
    protected $with = [
        'product'
    ];

    /**
     * @var array
     */
    public $translatedAttributes = ['name', 'description'];

    /**
     * @var array
     */
    protected $fillable = ['price', 'started_at', 'ended_at', 'image'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @ Offer Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }


}
