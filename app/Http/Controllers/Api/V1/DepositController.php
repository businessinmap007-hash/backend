<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Deposit\CreateDepositRequest;
use App\Http\Requests\Deposit\ConfirmDepositRequest;
use App\Http\Requests\Deposit\OutsideBimRequest;
use App\Http\Resources\EscrowResource;
use App\Models\Escrow;
use App\Models\User;
use App\Services\EscrowService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
{
    protected $wallet;
    protected $escrow;

    public function __construct(WalletService $wallet, EscrowService $escrow)
    {
        $this->wallet  = $wallet;
        $this->escrow  = $escrow;
    }

    /**
     * ============================================================
     * 1️⃣ إنشاء عملية Deposit / Escrow بين client ↔ business
     * ============================================================
     */
    public function create(CreateDepositRequest $request)
    {
        $client   = $request->user();
        $business = User::findOrFail($request->business_id);

        // التأكد أن الطرفين لديهم محفظة
        $this->wallet->createWalletIfNotExists($client);
        $this->wallet->createWalletIfNotExists($business);

        // إنشاء escrow فعلي
        $escrow = $this->escrow->create(
            $client,
            $business,
            $request->client_amount,
            $request->business_amount,
            $request->order_id
        );

        return response()->json([
            'status'  => 200,
            'message' => 'Escrow created successfully',
            'data'    => new EscrowResource($escrow),
        ]);
    }

    /**
     * ============================================================
     * 2️⃣ تأكيد العميل للدفع داخل BIM
     * ============================================================
     */
    public function clientConfirm(ConfirmDepositRequest $request, $id)
    {
        $escrow = Escrow::findOrFail($id);
        $user   = $request->user();

        if ($escrow->from_user_id !== $user->id) {
            return response()->json(['status' => 403, 'message' => 'Unauthorized'], 403);
        }

        if (! $escrow->client_confirmed) {
            $escrow->client_confirmed = true;
            $escrow->save();
        }

        return response()->json([
            'status'  => 200,
            'message' => 'Client confirmed successfully',
            'data'    => new EscrowResource($escrow),
        ]);
    }

    /**
     * ============================================================
     * 3️⃣ تأكيد البزنس للدفع داخل BIM
     * ============================================================
     */
    public function businessConfirm(ConfirmDepositRequest $request, $id)
    {
        $escrow = Escrow::findOrFail($id);
        $user   = $request->user();

        if ($escrow->to_user_id !== $user->id) {
            return response()->json(['status' => 403, 'message' => 'Unauthorized'], 403);
        }

        if (! $escrow->business_confirmed) {
            $escrow->business_confirmed = true;
            $escrow->save();
        }

        return response()->json([
            'status'  => 200,
            'message' => 'Business confirmed successfully',
            'data'    => new EscrowResource($escrow),
        ]);
    }

    /**
     * ============================================================
     * 4️⃣ العميل يؤكد الدفع خارج BIM (كاش – تحويل بنك)
     * ============================================================
     */
    public function clientOutsideBim(OutsideBimRequest $request, $id)
    {
        $escrow = Escrow::findOrFail($id);

        if ($escrow->from_user_id !== $request->user()->id) {
            return response()->json(['status' => 403, 'message' => 'Unauthorized'], 403);
        }

        $escrow->client_outside_bim = true;
        $escrow->save();

        return response()->json([
            'status'  => 200,
            'message' => 'Client marked payment as done outside BIM',
            'data'    => new EscrowResource($escrow),
        ]);
    }

    /**
     * ============================================================
     * 5️⃣ البزنس يؤكد الدفع خارج BIM
     * ============================================================
     */
    public function businessOutsideBim(OutsideBimRequest $request, $id)
    {
        $escrow = Escrow::findOrFail($id);

        if ($escrow->to_user_id !== $request->user()->id) {
            return response()->json(['status' => 403, 'message' => 'Unauthorized'], 403);
        }

        $escrow->business_outside_bim = true;
        $escrow->save();

        return response()->json([
            'status'  => 200,
            'message' => 'Business marked payment as done outside BIM',
            'data'    => new EscrowResource($escrow),
        ]);
    }

    /**
     * ============================================================
     * 6️⃣ فتح نزاع Dispute
     * ============================================================
     */
    public function openDispute(Request $request, $id)
    {
        $escrow = Escrow::findOrFail($id);

        $escrow->status = 'dispute';
        $escrow->save();

        return response()->json([
            'status'  => 200,
            'message' => 'Dispute opened successfully',
            'data'    => new EscrowResource($escrow),
        ]);
    }
}
