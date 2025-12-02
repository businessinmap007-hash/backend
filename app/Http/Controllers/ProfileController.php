<?php

namespace App\Http\Controllers;

use App\Libraries\Main;
use App\Models\Agenttype;
use App\Models\Companytype;
use App\Models\Country;
use App\Models\Faq;
use App\Models\Image;
use App\Models\Location;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;
use App\Models\Order;
use App\Http\Helpers\Images;

class ProfileController extends Controller
{


    public $main;
    public $public_path;

    public function __construct(Main $main)
    {
        $this->main = $main;
        $this->public_path = 'files/uploads/';
    }

    public function profile()
    {
        $user = auth()->user();

        $countries = Location::country()->get();

        if ($user->type == 'admin'):
            return redirect(route('admin.home'));
        else:
            return view('profile.index')->with(compact('user', 'countries'));
        endif;
    }




    public function profileUpdateUser(Request $request)
    {
        $user = User::whereId(auth()->id())->first();
        // Get Input
        $postData = [
            'email' => $request->email,
            'phone' => $request->phone,
        ];
        // Declare Validation Rules.
        $valRules = [
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|unique:users,phone,' . $user->id,
        ];

        // Declare Validation Messages
        $valMessages = [
            'email.required' => trans('trans.field_required'),
            'email.unique' => trans('trans.email_unique'),
            'phone.required' => trans('trans.required'),
            'phone.unique' => trans('trans.phone_unique'),
        ];

        // Validate Input
        $valResult = Validator::make($postData, $valRules, $valMessages);

        if ($valResult->passes()) {

            $inputs = $request->except('_token');

            if ($request->hasFile('image'))
                $inputs['image'] = $this->public_path . Images::imageUploader($request->file('image'), $this->public_path);
            $user->fill($inputs);
            $user->update($inputs);
            if ($user) {
                return response()->json(['status' => 200, 'message' => __('trans.profile_updated'), 'url' => route('profile')]);
            } else {
                return response()->json([
                    'status' => 400,
                    "message" => "Something error..."
                ]);
            }
        } else {

            $errors = [];
            foreach ($valResult->messages()->all() as $message) {
                $errors[] = '<p>' . $message . '</p>';
            }

            return response()->json([
                'status' => 402,
                'errors' => $errors,
            ]);
        }


    }



    public function changePassword(Request $request)
    {

        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'newpassword' => 'required',
            'confirm_newpassword' => 'required|same:newpassword'
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('global.some_errors_happen'),
                ]
            );
        }

        $hashedPassword = $user->password;
        if (\Hash::check($request->old_password, $hashedPassword)) {
            //Change the password
            $user->fill([
                'password' => \Hash::make($request->newpassword)
            ])->save();

            return response()->json([
                'status' => 200,
                'message' => __('global.password_was_edited_successfully'),
                'data' => $user
            ]);
        } else {
            return response()->json([
                'status' => 400,
                'message' => __('global.old_password_is_incorrect'),
            ]);
        }


    }

    public function updatePhone(Request $request)
    {

        $user = auth()->user();


        $checkIsExsitsBefore = User::wherePhone($request->new_phone)->first();

        if ($checkIsExsitsBefore) {
            return response()->json([
                'status' => 402,
                "message" => __('trans.unique_phone')
            ]);
        }


        $user->phone = $request->new_phone;
        $actionCode = rand(1000, 9999);
        $actionCode = $user->actionCode($actionCode);
        $user->action_code = $actionCode;

        if ($user->save()) {
            tap($user)->update([
                'is_active' => 0
            ]);
            return response()->json([
                'status' => 200,
                "message" => __('trans.phone_updated_successfully')
            ]);
        }


    }






}
