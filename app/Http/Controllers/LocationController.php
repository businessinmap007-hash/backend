<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{

    /**
     * Get All Cities By Country Id
     * @@ Country Id Received In Request not as A parameter
     */
    public function getCities(Request $request)
    {

        $cities = Location::whereParentId($request->countryId)->get();
        return response()->json($cities);

    }
}
