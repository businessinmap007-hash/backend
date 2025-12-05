<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Http\Request;

class SocialLikeController extends Controller
{
    public function index(Request $request)
    {
        $like = Like::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'post_id' => $request->post_id
            ]
        );

        return response()->json([
            'status' => 200,
            'message' => 'Like toggled',
            'data' => $like
        ]);
    }
}
