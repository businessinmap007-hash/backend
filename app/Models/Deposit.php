<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $fillable = [
        'client_id',
        'business_id',
        'target_type',
        'target_id',
        'total_amount',
        'client_percent',
        'business_percent',
        'client_amount',
        'business_amount',
        'status',
        'client_confirmed',
        'business_confirmed',
        'client_outside_bim',
        'business_outside_bim',
        'released_at',
        'refunded_at',
    ];

    protected $casts = [
        'total_amount'         => 'float',
        'client_amount'        => 'float',
        'business_amount'      => 'float',
        'client_confirmed'     => 'bool',
        'business_confirmed'   => 'bool',
        'client_outside_bim'   => 'bool',
        'business_outside_bim' => 'bool',
        'released_at'          => 'datetime',
        'refunded_at'          => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function business()
    {
        return $this->belongsTo(User::class, 'business_id');
    }
}
