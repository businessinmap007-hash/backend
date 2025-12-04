<?php

namespace App\Http\Controllers\Api\V1\Menu;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\MenuItemSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuItemSizeController extends Controller
{
    public function store(Request $request, $itemId)
    {
        $item = MenuItem::where('business_id', $request->user()->id)->findOrFail($itemId);

        $validator = Validator::make($request->all(), [
            'name'  => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $size = MenuItemSize::create([
            'menu_item_id' => $item->id,
            'name'         => $request->name,
            'price'        => $request->price,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Size added',
            'data'    => $size
        ]);
    }

    public function delete(Request $request, $id)
    {
        $size = MenuItemSize::findOrFail($id);

        if ($size->item->business_id != $request->user()->id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $size->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Size deleted'
        ]);
    }
}
