<?php

namespace App\Http\Controllers\Api\V1\Social;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class SocialCommentController extends Controller
{
    public function index($postId)
    {
        return Comment::where('post_id', $postId)->latest()->get();
    }

    public function commentRepliesList($commentId)
    {
        return Comment::where('parent_id', $commentId)->get();
    }

    public function store(Request $request)
    {
        $comment = Comment::create($request->all());

        return response()->json([
            'status' => 200,
            'message' => 'Comment created',
            'data' => $comment
        ]);
    }

    public function commentReplies(Request $request, Comment $comment)
    {
        $reply = Comment::create([
            'user_id' => $request->user()->id,
            'post_id' => $comment->post_id,
            'parent_id' => $comment->id,
            'body' => $request->body
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Reply created',
            'data' => $reply
        ]);
    }
}
