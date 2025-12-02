<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Users\RegisterRequestForm;
use App\Http\Resources\UserResource;
use App\Models\Image;
use App\Models\Social;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Images;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{

    public $public_path;

    public function __construct(Request $request)
    {
//        $language = $request->headers->get('lang') ? $request->headers->get('lang') : 'ar';
//        app()->setLocale($language);
//
//        if (!$request->headers->get('lang')) {
//            app()->setLocale($request->lang);
//        }

        $this->public_path = 'files/uploads/';
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RegisterRequestForm $request)
    {


        $inputs = $request->except('api_token');
        $inputs['api_token'] = Str::random(120);
        $inputs['action_code'] = $this->actionCode(rand(1000, 9999));
        $inputs['code'] = $this->profileCode(rand(10000000, 99999999));
        $user = User::create($inputs);
        if ($user) {

            if (isset($request->businessOptions) && $request->businessOptions != "") {
                $options = explode(',', $request->businessOptions);
                $user->options()->attach($options);
            }
            $user->social()->create($request->only('facebook', 'twitter', 'linkedin', 'youtube', 'instagram'));

            if($user->type == "business")
                $user->subscriptions()->create(
                    [
                        "is_active" => 1,
                        "duration" => 1,
                        "price" => 0,
                        "finished_at" => Carbon::now()->addMonth(),
                    ]
                );
            else
                $user->subscriptions()->create(
                    [
                        "is_active" => 1,
                        "duration" => 1,
                        "price" => 0,
                        "finished_at" => NULL,
                    ]
                );

            $this->manageDevices($request, $user);
            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => UserResource::make($user),
                'token' => $user->api_token
            ]);
        }
    }


    public function actionCode($code)
    {
        $rand = User::where('action_code', $code)->first();
        if ($rand || $rand == '') {
            return rand(1000, 9999);
        } else {
            return $code;
        }
    }


    public function profileCode($code)
    {
        $rand = User::where('code', $code)->first();
        if ($rand || $rand == '') {
            return rand(10000000, 99999999);
        } else {
            return $code;
        }
    }


    /**
     * @param $request
     * @param null $user
     */
    private function manageDevices($request, $user = null)
    {
        if ($request->device_token) {
            $data = Device::where('device', $request->device_token)->first();
            if ($data) {
                $data->user_id = $user->id;
                $data->save();
            } else {
                $data = new Device;
                $data->device = $request->device_token ? $request->device_token : "";
                $data->user_id = $user->id;
                $data->device_type = $request->device_type ? $request->device_type : "";
                $data->save();
            }
        }
    }

}
