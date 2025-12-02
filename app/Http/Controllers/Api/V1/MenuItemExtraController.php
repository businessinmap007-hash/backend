<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\MenuItemExtra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuItemExtraController extends Controller
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

        $extra = MenuItemExtra::create([
            'menu_item_id' => $item->id,
            'name'         => $request->name,
            'price'        => $request->price,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Extra added',
            'data'    => $extra
        ]);
    }

    public function delete(Request $request, $id)
    {
        $extra = MenuItemExtra::findOrFail($id);

        if ($extra->item->business_id != $request->user()->id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $extra->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Extra deleted'
        ]);
    }
}
