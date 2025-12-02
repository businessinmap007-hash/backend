<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Models\Coupon;
use App\Models\Option;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OptionController extends Controller
{
    public $public_path;

    public function __construct()
    {
        $this->public_path = 'files/uploads/';
    }

    /**
     * Display a listing of User.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $results = Option::orderBy('created_at', 'desc')->get();
        return view('admin.options.index', compact('results'));

    }


    /**
     * Show the form for creating new User.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        /**
         * Return Slider View.
         */
        return view('admin.options.create');

    }

    /**
     * Store a newly created User in storage.
     *
     * @param \App\Http\Requests\StoreUsersRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $inputs = $request->except('_token');
        Option::create($inputs);

        return returnedResponse(200, 'تم إضافة للخيارات بنجاح', null, route('options.index'));
    }

    /**
     * Show the form for editing User.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $option = Option::findOrFail($id);
        return view('admin.options.edit', compact('option'));
    }

    /**
     * Update User in storage.
     *
     * @param \App\Http\Requests\UpdateUsersRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {



        $coupon = Option::findOrFail($id);
        $inputs = $request->except('_token', '_method');

        \DB::beginTransaction();
        try {
            $coupon->fill($inputs)->update($inputs);
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            return returnedResponse(400, 'Something was wrong!', null);
        }
        return returnedResponse(200, 'لقد تم بيانات الخيارات بنجاح', null, route('options.index'), ['type' => 'update']);

    }

    /**
     * Remove User from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {



        \DB::beginTransaction();
        try {
            $option = Option::findOrFail($id);
            $option->delete();
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            return returnedResponse(400, 'Something was wrong!', null);
        }


        return response()->json([
            'status' => true,
            'data' => [
                'id' => $id
            ]
        ]);
    }

}
