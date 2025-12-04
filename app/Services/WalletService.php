<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class WalletService
{
    /**
     * إنشاء محفظة إن لم تكن موجودة
     */
    public function createWalletIfNotExists(User $user)
    {
        if (!$user->wallet) {
            return Wallet::create([
                'user_id'        => $user->id,
                'balance'        => 0,
                'locked_balance' => 0,
            ]);
        }

        return $user->wallet;
    }

    /**
     * التحقق من PIN
     */
    public function verifyPin(User $user, string $pinCode): bool
    {
        if (!$user->pin_code) {
            return false;
        }

        return Hash::check($pinCode, $user->pin_code);
    }

    /**
     * تحديث PIN (بعد التحقق عبر OTP)
     */
    public function updatePin(User $user, string $pin)
    {
        $user->pin_code = Hash::make($pin);
        $user->save();
        return true;
    }

    /**
     * يجب أن يكون لديه PIN قبل أي عملية مالية حساسة
     */
    public function mustHavePin(User $user)
    {
        if (!$user->pin_code) {
            throw new \Exception("Wallet PIN is required to perform this action.");
        }
    }

    /**
     * التحقق من الرصيد
     */
    private function checkBalance(Wallet $wallet, float $amount)
    {
        if ($wallet->balance < $amount) {
            throw new \Exception("Insufficient balance.");
        }
    }

    /**
     * إنشاء سجل معاملة
     */
    private function createTransaction(User $user, float $amount, string $type, string $details = null)
    {
        return WalletTransaction::create([
            'user_id' => $user->id,
            'amount'  => $amount,
            'type'    => $type,
            'details' => $details,
        ]);
    }

    /**
     * إيداع رصيد
     */
    public function deposit(User $user, float $amount, string $reason = 'deposit')
    {
        $wallet = $this->createWalletIfNotExists($user);

        DB::transaction(function () use ($wallet, $user, $amount, $reason) {
            $wallet->balance += $amount;
            $wallet->save();

            $this->createTransaction($user, $amount, 'deposit', $reason);
        });

        return true;
    }

    /**
     * سحب رصيد (يتطلب PIN)
     */
    public function withdraw(User $user, float $amount, string $reason = 'withdraw')
    {
        $this->mustHavePin($user);

        $wallet = $this->createWalletIfNotExists($user);
        $this->checkBalance($wallet, $amount);

        DB::transaction(function () use ($wallet, $user, $amount, $reason) {
            $wallet->balance -= $amount;
            $wallet->save();

            $this->createTransaction($user, -$amount, 'withdraw', $reason);
        });

        return true;
    }

    /**
     * تجميد مبلغ (Escrow Lock) – يتطلب PIN
     */
    public function lockAmount(User $user, float $amount)
    {
        $this->mustHavePin($user);

        $wallet = $this->createWalletIfNotExists($user);
        $this->checkBalance($wallet, $amount);

        DB::transaction(function () use ($wallet, $user, $amount) {
            $wallet->balance        -= $amount;
            $wallet->locked_balance += $amount;
            $wallet->save();

            $this->createTransaction($user, $amount, 'lock', 'Amount locked for escrow');
        });

        return true;
    }

    /**
     * تحرير الأموال المجمدة (Escrow Release) – يتطلب PIN
     */
    public function releaseLocked(User $user, float $amount)
    {
        $this->mustHavePin($user);

        $wallet = $this->createWalletIfNotExists($user);

        if ($wallet->locked_balance < $amount) {
            throw new \Exception("Not enough locked funds to release.");
        }

        DB::transaction(function () use ($wallet, $user, $amount) {
            $wallet->locked_balance -= $amount;
            $wallet->balance        += $amount;
            $wallet->save();

            $this->createTransaction($user, $amount, 'release', 'Escrow amount released');
        });

        return true;
    }

    /**
     * تحويل رصيد بين مستخدمين – يتطلب PIN من المرسل فقط
     */
    public function transfer(User $from, User $to, float $amount, string $reason = 'transfer')
    {
        $this->mustHavePin($from);

        $senderWallet = $this->createWalletIfNotExists($from);
        $receiverWallet = $this->createWalletIfNotExists($to);

        $this->checkBalance($senderWallet, $amount);

        DB::transaction(function () use ($from, $to, $amount, $reason, $senderWallet, $receiverWallet) {

            // خصم من المرسل
            $senderWallet->balance -= $amount;
            $senderWallet->save();
            $this->createTransaction($from, -$amount, 'transfer_out', "Transfer to user {$to->id}");

            // إضافة للمستقبل
            $receiverWallet->balance += $amount;
            $receiverWallet->save();
            $this->createTransaction($to, $amount, 'transfer_in', "Received from user {$from->id}");
        });

        return true;
    }
}
