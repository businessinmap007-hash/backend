<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Category;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Auth;
use App\Models\Device;
use App\Http\Helpers\Main;
use Illuminate\Support\Facades\App;


class LoginController extends Controller
{

    public $main;

    public function __construct(Main $main, Request $request)
    {

        $language = $request->headers->get('lang') ? $request->headers->get('lang') : 'ar';
        app()->setLocale($language);
        if (!$request->headers->get('lang')) {
            app()->setLocale($request->lang);
        }
        $this->main = $main;

    }


    public function update_device_token(Request $request)
    {
        $api_token = str_replace('Bearer ', '', request()->headers->get('Authorization'));
        $user = User::whereApiToken($api_token)->first();

        $devices = Device::pluck('device')->toArray();
        $data = in_array($request->deviceToken, $devices);


        if ($data) {
            $device = Device::where('user_id', $user->id)->where('device_type', $request->deviceType)->first();
            $device->device = $request->deviceToken;
            $device->save();
        } else {
            $device = new Device();
            $device->user_id = $user->id;
            $device->device = $request->deviceToken;
            $device->device_type = $request->deviceType ? $request->deviceType : "";
            $device->save();
        }

        return response()->json([
            'status' => 200,
            'message' => "Device token updated successfully",
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 402,
                    'errors' => $validator->errors()->all(),
                    'message' => 'Something went wrong!',
                ]
            );
        }




        if ($user = Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            if (!auth()->user()->api_token) {
                auth()->user()->api_token = strtolower(str_random(120));
                auth()->user()->save();
            }


            $this->manageDevices($request, auth()->user());

            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' =>  UserResource::make(auth()->user()),
                'api_token' => auth()->user()->api_token
            ]);
        } else {
            return response()->json([
                'status' => 400,
                'message' => "email or password incorrect.",
            ], 400);
        }
    }


    public function postActivationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'activation_code' => 'required',
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


        $user = User::where([
            'phone' => $request->phone,
        ])
            ->first();

        if ($user->action_code != $request->activation_code) {
            return response()->json([
                'status' => 400,
                'message' => 'Please check activation code',
            ]);
        }

        if ($user && $user->is_active == 0 && $user->action_code == $request->activation_code) {
            $user->is_active = 1;
            $user->save();
            return response()->json([
                'status' => 200,
                'message' => __('global.your_account_was_activated'),
                'data' => $user
            ]);
        } elseif ($user && $user->is_active == 1) {
            return response()->json([
                'status' => 200,
                'message' => __('global.your_account_was_activated_before'),
                'data' => $user
            ]);
        } else {
            return response()->json([
                'status' => 400,
                'message' => __('global.activation_code_not_correct'),
            ]);
        }


    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @@ Resend Activation Code.
     */

    public function resendActivationCode(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'phone' => 'required',
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


        $user = User::wherePhone($request->phone)->first();

        if (isset($user)) {
            $code = rand(1000, 9999);
            $activation_code = $user->actionCode($code);
            $user->action_code = $activation_code;
            if ($user->save()) {

                return response()->json([
                    'status' => 200,
                    'message' => __('global.activation_code_sent'),
                    'code' => $user->action_code
                ]);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => __('global.account_not_found'),
            ]);
        }


    }

    /**
     * @param $request
     * @@ User Device Management
     */
    private function manageDevices($request, $user = null)
    {
        if ($request->deviceToken) {
            $devices = Device::pluck('device')->toArray();
            $data = in_array($request->deviceToken, $devices);
            if ($data) {
                $data = Device::where('device', $request->deviceToken)->first();
                $data->user_id = $user->id;
                $data->save();
            } else {
                $data = new Device;
                $data->device = $request->deviceToken;
                $data->user_id = $user->id;
                $data->device_type = $request->deviceType ? $request->deviceType : "";
                $data->save();
            }
        }
    }
}
