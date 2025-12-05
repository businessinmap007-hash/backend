<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\WalletPinService;
use Illuminate\Http\Request;

class WalletPinController extends Controller
{
    protected $pin;

    public function __construct(WalletPinService $pin)
    {
        $this->pin = $pin;
    }

    public function setPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:6'
        ]);

        $this->pin->setPin($request->user(), $request->pin);

        return response()->json([
            'status'  => 200,
            'message' => 'PIN set successfully'
        ]);
    }

    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:6'
        ]);

        $this->pin->validatePinOrFail($request->user(), $request->pin);

        return response()->json([
            'status'  => 200,
            'message' => 'PIN is valid'
        ]);
    }
}
