<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuItemController extends Controller
{
    // ================================
    // Get Menu Items (Business Items)
    // ================================
    public function index(Request $request)
    {
        $items = MenuItem::where('business_id', $request->user()->id)
            ->with(['sizes', 'extras'])
            ->orderBy('sort_order', 'ASC')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $items
        ]);
    }

    // ================================
    // Create New Menu Item
    // ================================
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string',
            'image'   => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu', 'public');
        }

        $item = MenuItem::create([
            'business_id'    => $request->user()->id,
            'name_ar'        => $request->name_ar,
            'name_en'        => $request->name_en,
            'description_ar' => $request->description_ar,
            'description_en' => $request->description_en,
            'image'          => $imagePath,
            'sort_order'     => $request->sort_order ?? 0,
            'is_active'      => 1,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Item created successfully',
            'data'    => $item
        ]);
    }

    // ================================
    // Update Menu Item
    // ================================
    public function update(Request $request, $id)
    {
        $item = MenuItem::where('business_id', $request->user()->id)->findOrFail($id);

        $item->update($request->only(
            'name_ar',
            'name_en',
            'description_ar',
            'description_en',
            'sort_order',
            'is_active'
        ));

        return response()->json([
            'status'  => true,
            'message' => 'Item updated successfully',
            'data'    => $item
        ]);
    }

    // ================================
    // Search Menu Items
    // ================================
    public function search(Request $request)
    {
        $query = MenuItem::query()->with(['sizes', 'extras']);

        if ($request->search) {
            $text = $request->search;
            $query->where(function ($q) use ($text) {
                $q->where('name_ar', 'LIKE', "%$text%")
                  ->orWhere('name_en', 'LIKE', "%$text%");
            });
        }

        if ($request->business_id) {
            $query->where('business_id', $request->business_id);
        }

        if ($request->min_price) {
            $query->whereHas('sizes', function ($q) use ($request) {
                $q->where('price', '>=', $request->min_price);
            });
        }

        if ($request->max_price) {
            $query->whereHas('sizes', function ($q) use ($request) {
                $q->where('price', '<=', $request->max_price);
            });
        }

        if ($request->order_by == 'price') {
            $query->withMin('sizes', 'price')->orderBy('sizes_min_price');
        }

        if ($request->order_by == 'newest') {
            $query->orderBy('created_at', 'DESC');
        }

        return response()->json([
            'status' => true,
            'data'   => $query->get()
        ]);
    }

    // ================================
    // Delete Menu Item
    // ================================
    public function delete(Request $request, $id)
    {
        $item = MenuItem::where('business_id', $request->user()->id)->findOrFail($id);
        $item->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Item deleted successfully'
        ]);
    }
}
