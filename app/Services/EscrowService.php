<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Escrow;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EscrowService
{
    /**
     * إنشاء Escrow بين عميل وبزنس
     */
    public function create(
        User $client,
        User $business,
        float $clientAmount,
        float $businessAmount,
        ?int $orderId = null
    ): Escrow {

        if (! $client->isClient()) {
            throw ValidationException::withMessages([
                'client' => 'The from_user must be account_type=client',
            ]);
        }

        if (! $business->isBusiness()) {
            throw ValidationException::withMessages([
                'business' => 'The to_user must be account_type=business',
            ]);
        }

        return DB::transaction(function () use ($client, $business, $clientAmount, $businessAmount, $orderId) {

            // 🔐 حجز أموال العميل
            $this->lockAmount(
                $client,
                $clientAmount,
                'escrow_lock',
                "Client escrow for order #{$orderId}"
            );

            // 🔐 حجز أموال البزنس
            $this->lockAmount(
                $business,
                $businessAmount,
                'escrow_lock',
                "Business escrow for order #{$orderId}"
            );

            // 🧾 إنشاء سجل Escrow
            return Escrow::create([
                'from_user_id'     => $client->id,
                'to_user_id'       => $business->id,
                'client_amount'    => $clientAmount,
                'business_amount'  => $businessAmount,
                'order_id'         => $orderId,
                'status'           => 'pending',
            ]);
        });
    }


    /**
     * تحرير الأموال للطرفين (تم الدفع خارج التطبيق)
     */
    public function release(Escrow $escrow): Escrow
    {
        if ($escrow->status !== 'pending') {
            throw ValidationException::withMessages([
                'escrow' => 'Cannot release a non-pending escrow',
            ]);
        }

        return DB::transaction(function () use ($escrow) {

            $client   = $escrow->fromUser;
            $business = $escrow->toUser;

            // ✔ إرجاع الأموال للطرفين لأنها دفعت خارج التطبيق
            $this->unlockAmount($client,   $escrow->client_amount,   'escrow_release');
            $this->unlockAmount($business, $escrow->business_amount, 'escrow_release');

            $escrow->status = 'released';
            $escrow->save();

            return $escrow;
        });
    }


    /**
     * إلغاء Escrow – وإرجاع المال حسب اختيار الطرف المتحكم
     */
    public function cancel(
        Escrow $escrow,
        bool $refundClient,
        bool $refundBusiness
    ): Escrow {

        if ($escrow->status !== 'pending') {
            throw ValidationException::withMessages([
                'escrow' => 'Cannot cancel a non-pending escrow',
            ]);
        }

        return DB::transaction(function () use ($escrow, $refundClient, $refundBusiness) {

            $client   = $escrow->fromUser;
            $business = $escrow->toUser;

            if ($refundClient) {
                $this->unlockAmount($client, $escrow->client_amount, 'escrow_cancel');
            }

            if ($refundBusiness) {
                $this->unlockAmount($business, $escrow->business_amount, 'escrow_cancel');
            }

            $escrow->status = 'cancelled';
            $escrow->save();

            return $escrow;
        });
    }


    /* ==========================================================
     *  Helpers: wallet locking / unlocking
     * ==========================================================
     */

    protected function lockAmount(User $user, float $amount, string $type, string $description = '')
    {
        if ($amount <= 0) return;

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'locked_balance' => 0]
        );

        if ($wallet->balance < $amount) {
            throw ValidationException::withMessages([
                'wallet' => 'Insufficient wallet balance',
            ]);
        }

        // 🔐 حجز الرصيد
        $wallet->balance        -= $amount;
        $wallet->locked_balance += $amount;
        $wallet->save();

        WalletTransaction::create([
            'wallet_id'   => $wallet->id,
            'type'        => $type,
            'direction'   => 'out',
            'amount'      => $amount,
            'description' => $description,
        ]);
    }

    protected function unlockAmount(User $user, float $amount, string $type)
    {
        if ($amount <= 0) return;

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'locked_balance' => 0]
        );

        if ($wallet->locked_balance < $amount) {
            throw ValidationException::withMessages([
                'wallet' => 'Not enough locked balance',
            ]);
        }

        // 🔓 تحرير الرصيد
        $wallet->locked_balance -= $amount;
        $wallet->balance        += $amount;
        $wallet->save();

        WalletTransaction::create([
            'wallet_id'   => $wallet->id,
            'type'        => $type,
            'direction'   => 'in',
            'amount'      => $amount,
            'description' => "Escrow action: {$type}",
        ]);
    }
}
