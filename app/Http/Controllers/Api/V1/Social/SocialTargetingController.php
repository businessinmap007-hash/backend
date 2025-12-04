<?php

namespace App\Http\Controllers\Api\V1\Social;

use App\Http\Controllers\Controller;
use App\Models\Target;
use Illuminate\Http\Request;

class SocialTargetingController extends Controller
{
    public function index(Request $request)
    {
        return Target::where('user_id', $request->user()->id)->get();
    }

    public function store(Request $request)
    {
        $target = Target::updateOrCreate([
            'user_id' => $request->user()->id,
            'target_id' => $request->target_id
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Target updated',
            'data' => $target
        ]);
    }

    public function storeTargetCategories(Request $request)
    {
        // logic
    }
}
