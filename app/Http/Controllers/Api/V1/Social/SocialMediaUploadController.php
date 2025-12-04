<?php

namespace App\Http\Controllers\Api\V1\Social;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SocialMediaUploadController extends Controller
{
    public function store(Request $request)
    {
        return response()->json(['status' => 200, 'message' => 'Images uploaded']);
    }

    public function fileUploader(Request $request)
    {
        return response()->json(['status' => 200, 'message' => 'Image uploaded']);
    }
}
