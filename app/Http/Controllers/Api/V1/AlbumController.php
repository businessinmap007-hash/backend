<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Http\Request;

class SocialAlbumController extends Controller
{
    public function store(Request $request)
    {
        $album = Album::create($request->all());

        return response()->json(['status' => 200, 'data' => $album]);
    }

    public function update(Request $request, Album $album)
    {
        $album->update($request->all());
        return response()->json(['status' => 200]);
    }

    public function destroy(Album $album)
    {
        $album->delete();
        return response()->json(['status' => 200]);
    }
}
