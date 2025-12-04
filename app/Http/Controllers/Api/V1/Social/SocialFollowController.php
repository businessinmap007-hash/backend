<?php

namespace App\Http\Controllers\Api\V1\Social;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use Illuminate\Http\Request;

class SocialFollowController extends Controller
{
    public function index(Request $request)
    {
        return Follow::where('user_id', $request->user()->id)->get();
    }

    public function store(Request $request)
    {
        $follow = Follow::updateOrCreate([
            'user_id' => $request->user()->id,
            'follow_id' => $request->follow_id
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Follow updated',
            'data' => $follow
        ]);
    }

    public function storeCategoryFollow(Request $request)
    {
        // logic for following categories
    }
}
