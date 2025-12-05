<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use App\Services\WalletPinService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WalletController extends Controller
{
    protected $wallet;
    protected $pin;

    public function __construct(WalletService $wallet, WalletPinService $pin)
    {
        $this->wallet = $wallet;
        $this->pin    = $pin;
    }

    /**
     * 🔐 عرض رصيد المحفظة
     */
    public function balance(Request $request)
    {
        $wallet = $this->wallet->createWalletIfNotExists($request->user());

        return response()->json([
            'status'  => 200,
            'balance' => $wallet->balance,
            'locked'  => $wallet->locked_balance,
        ]);
    }

    /**
     * 💰 إيداع رصيد
     */
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $this->wallet->deposit($request->user(), $request->amount, "manual_deposit");

        return response()->json([
            'status'  => 200,
            'message' => 'Amount deposited successfully',
        ]);
    }

    /**
     * 🔻 سحب – يتطلب PIN + Lockout
     */
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'pin'    => 'required|digits:6'
        ]);

        $user = $request->user();

        // تحقق من PIN ونظام الإغلاق التلقائي
        $this->pin->validatePinOrFail($user, $request->pin);

        // تنفيذ عملية السحب
        $this->wallet->withdraw($user, $request->amount, "manual_withdraw");

        return response()->json([
            'status'  => 200,
            'message' => 'Amount withdrawn successfully',
        ]);
    }

    /**
     * 🔁 تحويل بين مستخدمين – يتطلب PIN
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'amount'     => 'required|numeric|min:1',
            'pin'        => 'required|digits:6'
        ]);

        $user = $request->user();

        // PIN Check
        $this->pin->validatePinOrFail($user, $request->pin);

        // تحويل الرصيد
        $this->wallet->transfer($user, 
            \App\Models\User::find($request->to_user_id),
            $request->amount,
            "manual_transfer"
        );

        return response()->json([
            'status'  => 200,
            'message' => 'Transfer completed successfully',
        ]);
    }

    /**
     * 📜 سجل معاملات المحفظة
     */
    public function transactions(Request $request)
    {
        $user = $request->user();

        $data = $user->wallet
            ? $user->wallet->transactions()->latest()->get()
            : [];

        return response()->json([
            'status'       => 200,
            'transactions' => $data
        ]);
    }
}
