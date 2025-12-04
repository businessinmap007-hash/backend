<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance', 'locked_balance'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // إضافة رصيد
    public function deposit($amount)
    {
        $this->balance += $amount;
        $this->save();
    }

    // سحب رصيد
    public function withdraw($amount)
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }

        $this->balance -= $amount;
        $this->save();
    }

    // حجز مبلغ (Escrow)
    public function lockAmount($amount)
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }

        $this->balance -= $amount;
        $this->locked_balance += $amount;
        $this->save();
    }

    // فك الحجز
    public function releaseLocked($amount)
    {
        if ($this->locked_balance < $amount) {
            throw new \Exception('Locked amount insufficient');
        }

        $this->locked_balance -= $amount;
        $this->balance += $amount;
        $this->save();
    }
}
