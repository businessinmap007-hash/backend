<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Users\UpdateProfileFormRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\Users\UserInfoResource;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Validator;
use App\Http\Helpers\Images;

class ProfileController extends Controller
{

    public $public_path;

    public function __construct(Request $request)
    {
        $language = $request->headers->get('lang') ? $request->headers->get('lang') : 'ar';
        app()->setLocale($language);
        $this->public_path = 'files/uploads/';

    }

    public function index(Request $request)
    {

        $user = User::whereId($request->user()->id)->first();
        return UserResource::make($user)->additional(['message' => "User Profile", 'status' => 200]);
    }


    public function updateProfile(UpdateProfileFormRequest $request)
    {

        $user = $request->user();

        $inputs = $request->all();
        // Check if User Not Found Return Unauthenticated message.
        if (!$user)
            return response()->json(["status" => 401, "message" => "Unauthorization"]);

        $user->social()->update($request->only('facebook', 'twitter', 'linkedin', 'youtube', 'instagram'));

        $user->fill($inputs)->update($inputs);

        if (isset($request->businessOptions)){
            $options = explode(',', $request->businessOptions);
            $user->options()->sync($options);
        }


        return UserResource::make($user)->additional(['status' => 200, 'message' => "Message List"]);


    }


    public function updateLanguage(Request $request)
    {
        $api_token = str_replace('Bearer ', '', request()->headers->get('Authorization'));
        $user = User::whereApiToken($api_token)->first();
        $language = $request->headers->get('lang') ? $request->headers->get('lang') : 'ar';
        $user->lang = $language;

        if ($user->save()) {

            return response()->json([
                'status' => 200,
            ]);

        }
    }


    public function logout(Request $request)
    {
        $api_token = str_replace('Bearer ', '', request()->headers->get('Authorization'));
        $user = User::whereApiToken($api_token)->first();


        $currentDevice = $user->devices()->whereDevice($request->deviceId)->first();


        if ($currentDevice->delete()) {
            return response()->json([
                'status' => 200,
            ]);

        }


    }


    public function updatePhone(Request $request)
    {
        $api_token = str_replace('Bearer ', '', request()->headers->get('Authorization'));
        $user = User::whereApiToken($api_token)->first();

        $validator = Validator::make($request->all(), [
            'newPhone' => 'required|unique:users,phone',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 402,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('global.some_errors_happen'),
                ]
            );
        }

        if ($request->type == "check") {
            if ($user->is_suspend == 0 && $user->is_active == 1) {
                $actionCode = rand(1000, 9999);
                $actionCode = $user->actionCode($actionCode);
                $user->action_code = $actionCode;
                if ($user->save()) {
                    return response()->json([
                        'status' => 200,
                        'message' => "activation correct",
                        'code' => $user->action_code,
                    ]);
                }
            }
        } else {

            $activationCode = $request->activationCode;
            if ($activationCode == $user->action_code) {
                $phone = $request->newPhone;
                $user = tap($user)->update([
                    'phone' => $phone,
                ]);

                if ($user) {

                    $userInfo = $user->generalUserInfo();

                    if ($user->userType() == 'company' && $user->is_completed == 1) {
                        $userInfo = $user->companyUserToArray();
                    } elseif ($user->userType() == 'driver') {
                        $userInfo = $user->driverUserToArray();
                    }
                    if ($user->userType() == 'client') {
                        $userInfo = $user->clientToArray();
                    }

                    return response()->json([
                        'status' => 200,
                        'message' => __('trans.phoneUpdatedSuccessfully'),
                        'data' => $userInfo
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => "incorrect activation",
                    'code' => $user->action_code,
                ]);
            }
        }
        return response()->json([
            'status' => 200,
        ]);


    }


    public function getProfileInformation(Request $request)
    {
        $userId = (int)$request->userId;
        $user = User::findOrFail($userId);
        return UserResource::make($user)->additional(['message' => "User Information", 'status' => 200]);

    }

}
