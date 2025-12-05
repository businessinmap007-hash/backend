<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class SocialPostController extends Controller
{
    public function getPosts()
    {
        return response()->json([
            'status' => 200,
            'data'   => Post::latest()->paginate(20)
        ]);
    }

    public function index($id)
    {
        return response()->json([
            'status' => 200,
            'data'   => Post::where('id', $id)->get()
        ]);
    }

    public function store(Request $request)
    {
        $post = Post::create($request->all());

        return response()->json([
            'status' => 200,
            'message' => 'Post created successfully',
            'data' => $post
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $post->update($request->all());

        return response()->json([
            'status' => 200,
            'message' => 'Post updated',
            'data' => $post
        ]);
    }

    public function delete(Post $post)
    {
        $post->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Post deleted'
        ]);
    }

    public function sharePost(Request $request, Post $post)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Post shared'
        ]);
    }
}
