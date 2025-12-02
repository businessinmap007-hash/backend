<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// عدّل الأسماء دي حسب الموديلات الموجودة عندك فعليًا
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\MenuOrder;
use App\Models\MenuOrderItem;

class MenuOrderController extends Controller
{
    /**
     * إنشاء طلب منيو جديد من الكارت
     * POST /api/v1/menu/orders/from-cart
     */
    public function createFromCart(Request $request)
    {
        $request->validate([
            'cart_id'        => 'required|integer|exists:carts,id',
            'address'        => 'required|string',
            'payment_method' => 'required|string',
            'notes'          => 'nullable|string',
        ]);

        $user = $request->user();

        $cart = Cart::with('items')
            ->where('id', $request->cart_id)
            ->where('user_id', $user->id)
            ->where('status', 'checked_out') // من Checkout
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        return DB::transaction(function () use ($cart, $request, $user) {

            // حساب الإجمالي (لو مش مخزون في cart)
            $total = $cart->items->sum(function ($row) {
                return $row->price * $row->qty;
            });

            // إنشاء الطلب الرئيسي
            $order = MenuOrder::create([
                'user_id'        => $user->id,
                'business_id'    => $cart->business_id,
                'cart_id'        => $cart->id,
                'total_price'    => $total,
                'payment_method' => $request->payment_method,
                'address'        => $request->address,
                'notes'          => $request->notes,
                'status'         => 'pending', // pending / accepted / preparing / delivering / completed / cancelled
            ]);

            // نسخ عناصر الكارت إلى عناصر الطلب
            foreach ($cart->items as $item) {
                MenuOrderItem::create([
                    'menu_order_id' => $order->id,
                    'menu_item_id'  => $item->menu_item_id,
                    'qty'           => $item->qty,
                    'size'          => $item->size,
                    'unit_price'    => $item->price,
                    'total_price'   => $item->price * $item->qty,
                ]);
            }

            // تحديث حالة الكارت (منتهٍ)
            $cart->status = 'ordered';
            $cart->save();

            return response()->json([
                'status'  => true,
                'message' => 'Menu order created from cart',
                'data'    => $order->load('items')
            ]);
        });
    }

    /**
     * طلبات المستخدم (Menu Orders)
     * GET /api/v1/menu/orders/my
     */
    public function myOrders(Request $request)
    {
        $orders = MenuOrder::with('items')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $orders
        ]);
    }

    /**
     * طلبات البزنس (Menu Orders)
     * GET /api/v1/menu/orders/business
     */
    public function businessOrders(Request $request)
    {
        $businessId = $request->user()->id; // لو عندك نظام بزنس مختلف عدّلها

        $orders = MenuOrder::with('items', 'user')
            ->where('business_id', $businessId)
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $orders
        ]);
    }

    /**
     * عرض تفاصيل طلب واحد
     * GET /api/v1/menu/orders/{id}
     */
    public function show(Request $request, $id)
    {
        $order = MenuOrder::with('items')
            ->where('id', $id)
            ->where(function ($q) use ($request) {
                $q->where('user_id', $request->user()->id)
                  ->orWhere('business_id', $request->user()->id);
            })
            ->firstOrFail();

        return response()->json([
            'status' => true,
            'data'   => $order
        ]);
    }

    /**
     * تحديث حالة الطلب (بزنس أو سيستم)
     * POST /api/v1/menu/orders/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string', // pending / accepted / preparing / delivering / completed / cancelled
        ]);

        $order = MenuOrder::findOrFail($id);

        // هنا ممكن تضيف تشيك إن اللى بيغير الحالة هو البزنس المالك أو أدمن
        $order->status = $request->status;
        $order->save();

        return response()->json([
            'status'  => true,
            'message' => 'Order status updated',
            'data'    => $order
        ]);
    }
}
