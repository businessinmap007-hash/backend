<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escrow extends Model
{
    use HasFactory;

    protected $table = 'escrows';

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'amount',
        'order_id',
        'status',
    ];

    /**
     * المستخدم الذي دفع الدفعة المقدمة
     */
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * المستخدم المستهدف (الطرف الآخر)
     */
    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    /**
     * العملية/التعاقد المرتبط بالمبلغ
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
