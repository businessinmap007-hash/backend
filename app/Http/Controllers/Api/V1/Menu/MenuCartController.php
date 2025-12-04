<?php

namespace App\Http\Controllers\Api\V1\Menu;


use App\Http\Controllers\Controller;
use App\Models\MenuCart;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuCartController extends Controller
{
    /**
     * عرض محتوى السلة
     */
    public function index(Request $request)
    {
        $cart = MenuCart::where('user_id', $request->user()->id)
                        ->with('menuItem')
                        ->get();

        return response()->json([
            'status' => true,
            'data' => $cart
        ]);
    }

    /**
     * إضافة عنصر منيو للسلة
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'menu_item_id' => 'required|exists:menu_items,id',
            'business_id'  => 'required|exists:users,id',
            'qty'          => 'nullable|integer|min:1',
            'size'         => 'nullable|string',
            'addons'       => 'nullable|array',
            'options'      => 'nullable|array',
            'notes'        => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $item = MenuItem::find($request->menu_item_id);

        // --- حساب سعر الإضافات ---
        $addonsTotal = 0;
        if ($request->addons) {
            foreach ($request->addons as $addon) {
                $addonsTotal += $addon['price'];
            }
        }

        // --- السعر النهائي ---
        $final_price = ($item->price + $addonsTotal) * ($request->qty ?? 1);

        // إنشاء السجل
        $cart = MenuCart::create([
            'user_id'        => $request->user()->id,
            'menu_item_id'   => $item->id,
            'business_id'    => $request->business_id,
            'qty'            => $request->qty ?? 1,
            'size'           => $request->size,
            'addons'         => $request->addons,
            'options'        => $request->options,
            'notes'          => $request->notes,
            'price_unit'     => $item->price,
            'price_addons'   => $addonsTotal,
            'price_total'    => $final_price,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Item added to cart',
            'data' => $cart
        ]);
    }

    public function list(Request $request)
    {
        $user = $request->user();

        $items = MenuCart::where('user_id', $user->id)
            ->with(['menuItem', 'size'])
            ->orderBy('id', 'desc')
            ->get();

        $total = $items->sum('total_price');

        return response()->json([
            'status'      => 200,
            'message'     => 'Cart items',
            'total_price' => $total,
            'items'       => $items,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'qty' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $cart = MenuCart::findOrFail($id);

        $cart->qty = $request->qty;
        $cart->total_price = $cart->unit_price * $request->qty;
        $cart->save();

        return response()->json([
            'status'  => 200,
            'message' => 'Cart updated',
            'data'    => $cart,
        ]);
    }



    /**
     * تحديث الكمية
     */
    public function updateQty(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required|exists:menu_cart,id',
            'qty'     => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cart = MenuCart::find($request->cart_id);

        // تعديل السعر النهائي
        $cart->qty = $request->qty;
        $cart->price_total = ($cart->price_unit + $cart->price_addons) * $request->qty;
        $cart->save();

        return response()->json([
            'status' => true,
            'message' => 'Quantity updated',
            'data' => $cart
        ]);
    }

    /**
     * حذف عنصر من السلة
     */
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required|exists:menu_cart,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        MenuCart::find($request->cart_id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Item removed'
        ]);
    }


    

    /**
     * حذف السلة بالكامل
     */
    public function clear(Request $request)
    {
        MenuCart::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Cart cleared'
        ]);
    }
}
