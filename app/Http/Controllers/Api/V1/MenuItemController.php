<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MenuItemController extends Controller
{
    /**
     * Get menu items for the authenticated business.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $businessId = $request->user()->id;

        $items = MenuItem::where('business_id', $businessId)
            ->with(['sizes', 'extras'])
            ->orderBy('sort_order', 'ASC')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $items,
        ]);
    }

    /**
     * Create a new menu item for the authenticated business.
     *
     * Expects at minimum:
     *  - name_ar (required)
     *  - image (optional, image file)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name_ar'        => ['required', 'string', 'max:255'],
            'name_en'        => ['nullable', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'sort_order'     => ['nullable', 'integer'],
            'image'          => ['nullable', 'image'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Handle image upload if exists
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu', 'public');
        }

        $item = MenuItem::create([
            'business_id'    => $request->user()->id,
            'name_ar'        => $data['name_ar'],
            'name_en'        => $data['name_en']        ?? null,
            'description_ar' => $data['description_ar'] ?? null,
            'description_en' => $data['description_en'] ?? null,
            'image'          => $imagePath,
            'sort_order'     => $data['sort_order']     ?? 0,
            'is_active'      => 1,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Item created successfully',
            'data'    => $item,
        ]);
    }

    /**
     * Update an existing menu item for the authenticated business.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int                       $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $businessId = $request->user()->id;

        $item = MenuItem::where('business_id', $businessId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name_ar'        => ['nullable', 'string', 'max:255'],
            'name_en'        => ['nullable', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'sort_order'     => ['nullable', 'integer'],
            'is_active'      => ['nullable', 'boolean'],
            'image'          => ['nullable', 'image'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Handle image upload if exists
        if ($request->hasFile('image')) {
            // Optional: remove old image if you want
            if (! empty($item->image)) {
                Storage::disk('public')->delete($item->image);
            }

            $data['image'] = $request->file('image')->store('menu', 'public');
        }

        $item->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Item updated successfully',
            'data'    => $item,
        ]);
    }

    /**
     * Search menu items with optional filters:
     *  - search: text in name_ar or name_en
     *  - business_id
     *  - min_price / max_price (based on related sizes.price)
     *  - order_by: price | newest
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $query = MenuItem::query()->with(['sizes', 'extras']);

        if ($request->filled('search')) {
            $text = $request->search;

            $query->where(function ($q) use ($text) {
                $q->where('name_ar', 'LIKE', "%{$text}%")
                  ->orWhere('name_en', 'LIKE', "%{$text}%");
            });
        }

        if ($request->filled('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        if ($request->filled('min_price')) {
            $query->whereHas('sizes', function ($q) use ($request) {
                $q->where('price', '>=', $request->min_price);
            });
        }

        if ($request->filled('max_price')) {
            $query->whereHas('sizes', function ($q) use ($request) {
                $q->where('price', '<=', $request->max_price);
            });
        }

        if ($request->order_by === 'price') {
            // Order by minimum size price
            $query->withMin('sizes', 'price')->orderBy('sizes_min_price');
        }

        if ($request->order_by === 'newest') {
            $query->orderBy('created_at', 'DESC');
        }

        $items = $query->get();

        return response()->json([
            'status' => true,
            'data'   => $items,
        ]);
    }

    /**
     * Delete a menu item for the authenticated business.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int                       $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, int $id): JsonResponse
    {
        $businessId = $request->user()->id;

        $item = MenuItem::where('business_id', $businessId)->findOrFail($id);

        // Optional: remove image file if stored
        if (! empty($item->image)) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Item deleted successfully',
        ]);
    }
}
