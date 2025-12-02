<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\Api\LocationResource;
use App\Models\Location;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocationController extends Controller
{

    public function __construct(Request $request)
    {
        $language = $request->headers->get('lang') ? $request->headers->get('lang') : 'ar';
        app()->setLocale($language);
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * /**
     * @@ Get Countries List
     */
    public function countries()
    {

        return LocationResource::collection(Location::country()->get())->additional(['status' => 200]);
    }

    /**
     * @param Location $location
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @get cities list by parent id (Location -- country...)
     */

    public function cities(Location $location)
    {
        return LocationResource::collection($location->children)->additional(['status' => 200]);
    }

}
