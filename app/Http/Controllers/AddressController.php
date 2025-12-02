<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Location;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $countries = Location::country()->get();
        return view('addresses.index')->with(compact('countries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $address = auth()->user()->addresses()->create($request->all());
        if ($address)
            return returnedResponse(200, "لقد تم إضافة العنوان بنجاح", null, route('addresses.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function updatePrimaryAddress(Request $request)
    {

        if ($request->addressId) {
            auth()->user()->addresses()->update(['is_primary' => 0]);
        }
        $address = auth()->user()->addresses()->findOrFail($request->addressId);
        $address->is_primary = 1;
        if ($address->save())
            return returnedResponse(200, __('trans.address_changed_to_primary'), $address, null);


    }
}
