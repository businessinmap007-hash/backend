<?php

namespace App\Http\Controllers\Api\V1\Menu;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\MenuItem;
use App\Models\MenuItemSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * الحصول على كارت المستخدم (اختياري: حسب business_id)
     */
    protected function getOrCreateCart($userId, $businessId)
    {
        return Cart::firstOrCreate([
            'user_id'     => $userId,
            'business_id' => $businessId,
        ], [
            'status' => 'active',
        ]);
    }

    // =========================
    // GET /api/v1/menu/cart
    // =========================
    public function getCart(Request $request)
    {
        $userId     = $request->user()->id;
        $businessId = $request->query('business_id'); // لو حابب تفصل كارت كل بزنس لوحده

        $query = Cart::where('user_id', $userId)
            ->where('status', 'active')
            ->with('items.menuItem');

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        $cart = $query->first();

        return response()->json([
            'status' => true,
            'data'   => $cart ?: ['items' => []],
        ]);
    }

    // =========================
    // POST /api/v1/menu/cart/items
    // =========================
    public function addItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id'  => 'required|integer',
            'menu_item_id' => 'required|integer|exists:menu_items,id',
            'qty'          => 'required|integer|min:1',
            'size'         => 'nullable|string',
            'addons'       => 'array',   // حالياً مش هنخزنها في جدول منفصل
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user()->id;

        return DB::transaction(function () use ($request, $userId) {

            $item = MenuItem::findOrFail($request->menu_item_id);

            // نجيب أو ننشئ كارت للبزنس ده
            $cart = $this->getOrCreateCart($userId, $request->business_id);

            // نحسب السعر على حسب الـ size لو موجودة
            $unitPrice = 0;

            if ($request->size) {
                $sizeRow = MenuItemSize::where('menu_item_id', $item->id)
                    ->where('name', $request->size) // أو 'size' حسب عمودك الحقيقي
                    ->first();

                if ($sizeRow) {
                    $unitPrice = $sizeRow->price;
                }
            }

            // لو مفيش size سعره 0 (تقدر تعدله لاحقاً)
            $unitPrice = $unitPrice ?: 0;

            $cartItem = CartItem::create([
                'cart_id'      => $cart->id,
                'user_id'      => $userId,
                'menu_item_id' => $item->id,
                'qty'          => $request->qty,
                'size'         => $request->size,
                'price'        => $unitPrice,
            ]);

            // لو حابب تخزن addons في المستقبل، تقدر تضيف جدول cart_item_addons هنا

            $cart->load('items.menuItem');

            return response()->json([
                'status'  => true,
                'message' => 'Item added to cart',
                'data'    => $cart
            ]);
        });
    }

    // =========================
    // PUT /api/v1/menu/cart/items/{item}
    // =========================
    public function updateItem(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'qty'  => 'nullable|integer|min:1',
            'size' => 'nullable|string',
            // 'addons' => 'array' // لو حابب تضيفها لاحقاً
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $userId   = $request->user()->id;
        $cartItem = CartItem::where('user_id', $userId)->findOrFail($id);

        if ($request->has('qty') && $request->qty) {
            $cartItem->qty = $request->qty;
        }

        if ($request->has('size') && $request->size) {
            $sizeRow = MenuItemSize::where('menu_item_id', $cartItem->menu_item_id)
                ->where('name', $request->size)
                ->first();

            if ($sizeRow) {
                $cartItem->size  = $request->size;
                $cartItem->price = $sizeRow->price; // استبدال السعر مش جمعه فوق القديم
            }
        }

        $cartItem->save();

        $cart = $cartItem->cart()->with('items.menuItem')->first();

        return response()->json([
            'status'  => true,
            'message' => 'Cart item updated',
            'data'    => $cart
        ]);
    }

    // =========================
    // DELETE /api/v1/menu/cart/items/{item}
    // =========================
    public function removeItem(Request $request, $id)
    {
        $userId   = $request->user()->id;
        $cartItem = CartItem::where('user_id', $userId)->findOrFail($id);
        $cart     = $cartItem->cart;

        $cartItem->delete();

        $cart->load('items.menuItem');

        return response()->json([
            'status'  => true,
            'message' => 'Item removed from cart',
            'data'    => $cart
        ]);
    }

    // =========================
    // DELETE /api/v1/menu/cart
    // =========================
    public function clearCart(Request $request)
    {
        $userId = $request->user()->id;

        CartItem::where('user_id', $userId)->delete();
        Cart::where('user_id', $userId)
            ->where('status', 'active')
            ->update(['status' => 'cleared']);

        return response()->json([
            'status'  => true,
            'message' => 'Cart cleared'
        ]);
    }

    // =========================
    // POST /api/v1/menu/cart/checkout
    // (مرحلة تلخيص الكارت – الربط مع Order هيكون في Controller تاني)
    // =========================
    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $userId = $request->user()->id;

        $cart = Cart::where('user_id', $userId)
            ->where('status', 'active')
            ->with('items')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        $total = $cart->items->sum(function ($row) {
            return $row->price * $row->qty;
        });

        $cart->total_price    = $total;
        $cart->payment_method = $request->payment_method;
        $cart->status         = 'checked_out';
        $cart->save();

        return response()->json([
            'status'  => true,
            'message' => 'Cart checkout summary',
            'data'    => [
                'cart_id'        => $cart->id,
                'total'          => $total,
                'payment_method' => $cart->payment_method,
            ]
        ]);
    }
}
