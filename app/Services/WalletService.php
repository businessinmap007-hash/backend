<?php

namespace App\Services;

use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class WalletPinService
{
    const MAX_ATTEMPTS = 5;
    const LOCK_MINUTES = 5;

    /**
     * إنشاء أو تحديث PIN
     */
    public function setPin(User $user, string $pin)
    {
        $user->pin_code = Hash::make($pin);
        $user->pin_attempts = 0;
        $user->pin_locked_until = null;
        $user->save();
        return true;
    }

    /**
     * التحقق من PIN مع نظام الإغلاق التلقائي
     */
    public function validatePinOrFail(User $user, string $pin)
    {
        // هل المستخدم مقفول؟
        if ($user->pin_locked_until && now()->lt($user->pin_locked_until)) {

            $minutesLeft = now()->diffInMinutes($user->pin_locked_until);

            throw ValidationException::withMessages([
                'pin' => "Wallet locked. Try again after {$minutesLeft} minutes."
            ]);
        }

        // PIN صحيح
        if (Hash::check($pin, $user->pin_code)) {
            $user->pin_attempts = 0;
            $user->pin_locked_until = null;
            $user->save();
            return true;
        }

        // PIN خاطئ → زيادة المحاولات
        $user->pin_attempts++;

        // إغلاق الحساب مؤقتاً
        if ($user->pin_attempts >= self::MAX_ATTEMPTS) {
            $user->pin_locked_until = now()->addMinutes(self::LOCK_MINUTES);
        }

        $user->save();

        throw ValidationException::withMessages([
            'pin' => 'Invalid PIN'
        ]);
    }
}
