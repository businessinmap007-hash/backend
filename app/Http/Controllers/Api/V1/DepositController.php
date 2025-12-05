<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Services\EscrowService;

class DepositController extends Controller
{
    protected $escrowService;

    public function __construct(EscrowService $escrowService)
    {
        $this->escrowService = $escrowService;
    }

    /**
     * ▶ إنشاء دفعة مقدمة (Deposit)
     */
    public function create(Request $request)
    {
        $request->validate([
            'business_id'     => 'required|exists:users,id',
            'client_amount'   => 'required|numeric|min:0.1',
            'business_amount' => 'required|numeric|min:0',
            'order_id'        => 'nullable|exists:orders,id'
        ]);

        // المستخدم الحالي = العميل
        $client = $request->user();
        $business = User::find($request->business_id);

        try {
            $escrow = $this->escrowService->create(
                $client,
                $business,
                $request->client_amount,
                $request->business_amount,
                $request->order_id
            );

            return response()->json([
                'status'  => 200,
                'message' => 'Deposit (Escrow) created successfully.',
                'data'    => $escrow
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * ▶ العميل يؤكد أنه دفع خارج BIM
     */
    public function clientOutsideBim(Request $request, $id)
    {
        $escrow = $this->getEscrow($id, $request->user());

        $escrow->client_paid_outside = true;
        $escrow->save();

        return response()->json([
            'status'  => 200,
            'message' => 'Client confirmed outside-BIM payment.'
        ]);
    }

    /**
     * ▶ البزنس يؤكد أنه استلم خارج BIM
     */
    public function businessOutsideBim(Request $request, $id)
    {
        $escrow = $this->getEscrow($id, $request->user());

        $escrow->business_paid_outside = true;
        $escrow->save();

        return response()->json([
            'status'  => 200,
            'message' => 'Business confirmed outside-BIM payment.'
        ]);
    }

    /**
     * ▶ العميل يؤكد أن العملية تمت داخل BIM
     */
    public function clientConfirm(Request $request, $id)
    {
        $escrow = $this->getEscrow($id, $request->user());

        $escrow->client_confirm = true;
        $escrow->save();

        return response()->json([
            'status'  => 200,
            'message' => 'Client confirmed payment.'
        ]);
    }

    /**
     * ▶ البزنس يؤكد أن العملية تمت داخل BIM
     */
    public function businessConfirm(Request $request, $id)
    {
        $escrow = $this->getEscrow($id, $request->user());

        $escrow->business_confirm = true;
        $escrow->save();

        return response()->json([
            'status'  => 200,
            'message' => 'Business confirmed payment.'
        ]);
    }

    /**
     * ▶ فتح نزاع على العملية
     */
    public function openDispute(Request $request, $id)
    {
        $escrow = $this->getEscrow($id, $request->user());

        $escrow->status = 'disputed';
        $escrow->dispute_reason = $request->reason ?? null;
        $escrow->save();

        return response()->json([
            'status'  => 200,
            'message' => 'Dispute opened successfully.'
        ]);
    }

    /**
     * Helper جلب عملية Escrow والتحقق من صلاحية الوصول
     */
    protected function getEscrow($id, User $user)
    {
        $escrow = \App\Models\Escrow::findOrFail($id);

        if ($escrow->from_user_id !== $user->id && $escrow->to_user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        return $escrow;
    }
}
