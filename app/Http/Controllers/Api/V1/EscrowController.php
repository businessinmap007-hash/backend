<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Escrow\CreateEscrowRequest;
use App\Http\Requests\Escrow\CancelEscrowRequest;
use App\Models\Escrow;
use App\Models\User;
use App\Services\EscrowService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EscrowController extends Controller
{
    protected EscrowService $escrow;

    public function __construct(EscrowService $escrow)
    {
        $this->escrow = $escrow;
    }

    /**
     * إنشاء Escrow جديد بين عميل وبزنس
     */
    public function create(CreateEscrowRequest $request)
    {
        $client   = $request->user();
        $business = User::findOrFail($request->business_id);

        try {
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
                'data'    => $escrow,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * تحرير Escrow بعد إتمام عملية خارج التطبيق
     */
    public function release($id)
    {
        $escrow = Escrow::findOrFail($id);

        try {
            $updated = $this->escrow->release($escrow);

            return response()->json([
                'status'  => 200,
                'message' => 'Escrow released successfully',
                'data'    => $updated,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * إلغاء Escrow مع تحديد من يحصل على الاسترجاع
     */
    public function cancel(CancelEscrowRequest $request, $id)
    {
        $escrow = Escrow::findOrFail($id);

        try {
            $updated = $this->escrow->cancel(
                $escrow,
                $request->refund_client,
                $request->refund_business
            );

            return response()->json([
                'status'  => 200,
                'message' => 'Escrow cancelled successfully',
                'data'    => $updated,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * عرض تفاصيل Escrow
     */
    public function show($id)
    {
        $escrow = Escrow::findOrFail($id);

        return response()->json([
            'status'  => 200,
            'message' => 'Escrow details fetched successfully',
            'data'    => $escrow,
        ]);
    }


    /**
     * عرض جميع Escrows الخاصة بالمستخدم
     */
    public function myEscrows(Request $request)
    {
        $user = $request->user();

        $escrows = Escrow::where('from_user_id', $user->id)
                        ->orWhere('to_user_id', $user->id)
                        ->orderBy('id', 'DESC')
                        ->get();

        return response()->json([
            'status'  => 200,
            'message' => 'Escrows fetched successfully',
            'data'    => $escrows,
        ]);
    }
}
