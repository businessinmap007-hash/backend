<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\EscrowService;
use App\Models\Escrow;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EscrowController extends Controller
{
    protected $escrow;

    public function __construct(EscrowService $escrow)
    {
        $this->escrow = $escrow;
    }

    /**
     * ▶ إنشاء Escrow (client → business)
     */
    public function create(Request $request)
    {
        $request->validate([
            'business_id'      => 'required|exists:users,id',
            'client_amount'    => 'required|numeric|min:0',
            'business_amount'  => 'required|numeric|min:0',
            'order_id'         => 'nullable|exists:orders,id'
        ]);

        try {
            $escrow = $this->escrow->create(
                $request->user(),
                \App\Models\User::find($request->business_id),
                $request->client_amount,
                $request->business_amount,
                $request->order_id
            );

            return response()->json([
                'status'  => 200,
                'message' => 'Escrow created successfully.',
                'data'    => $escrow
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * ▶ عرض Escrow
     */
    public function show($id)
    {
        $escrow = Escrow::findOrFail($id);

        return response()->json([
            'status'  => 200,
            'escrow'  => $escrow,
        ]);
    }

    /**
     * ▶ عرض عمليات Escrow الخاصة بالمستخدم
     */
    public function myEscrows(Request $request)
    {
        $user = $request->user();

        $escrows = Escrow::where('from_user_id', $user->id)
            ->orWhere('to_user_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'status'  => 200,
            'escrows' => $escrows,
        ]);
    }

    /**
     * ▶ تحرير الأموال للطرفين (خارج التطبيق)
     */
    public function release(Request $request, $id)
    {
        $request->validate([
            'client_amount'   => 'required|numeric|min:0',
            'business_amount' => 'required|numeric|min:0',
        ]);

        $escrow = Escrow::findOrFail($id);

        try {
            $updated = $this->escrow->release(
                $escrow,
                $request->client_amount,
                $request->business_amount
            );

            return response()->json([
                'status'  => 200,
                'message' => 'Escrow released successfully.',
                'data'    => $updated
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * ▶ إلغاء Escrow
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'refund_client'   => 'required|boolean',
            'refund_business' => 'required|boolean',
            'client_amount'   => 'required|numeric|min:0',
            'business_amount' => 'required|numeric|min:0',
        ]);

        $escrow = Escrow::findOrFail($id);

        try {
            $updated = $this->escrow->cancel(
                $escrow,
                $request->refund_client,
                $request->refund_business,
                $request->client_amount,
                $request->business_amount
            );

            return response()->json([
                'status'  => 200,
                'message' => 'Escrow cancelled successfully.',
                'data'    => $updated
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'errors' => $e->errors()
            ], 422);
        }
    }
}
