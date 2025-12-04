<?php

namespace App\Http\Controllers\Api\V1\Business;

use App\Http\Requests\Sponsors\PostSponsorFormRequest;
use App\Http\Resources\Sponsors\SponsorsIndexResource;
use App\Libraries\Main;
use App\Models\Setting;
use App\Models\Sponsor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Images;

class SponsorController extends Controller
{

    public $public_path;
    public $config;

    public function __construct(Main $config)
    {
        $this->public_path = 'files/uploads/';
        $this->config = $config;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $ads = array('paid' => SponsorsIndexResource::collection($user->sponsors()->whereDate('expire_at', '>', Carbon::now())->where('type', 'paid')->get()),
            'free' => SponsorsIndexResource::collection($user->sponsors()->where('type', 'free')->get()));
        return response()->json(['status' => 200, 'data' => $ads, 'message' => "Sponsors Lists."]);
    }

    public function store(PostSponsorFormRequest $request)
    {

        $setting = new Setting();
        $user = $request->user();
        $date = Carbon::parse($request->expire_at);
        $diff = (int)$date->diffInDays(Carbon::now());
        $costPerDay = $setting->getBody('ad_cost');
        $adCost = $diff * $costPerDay;

        if ($this->config->calculateUserBalance($user) < $adCost)
            return response()->json(['status' => 400, 'message' => "Sorry, Your balance less than cost of advertisement."]);

        $inputs = $request->validated();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $inputs['expire_at'] = $date;
        if ($user->sponsors()->create($inputs)) {

            $transactionData = [
                'status' => 'withdrawal',
                'price' => $adCost,
                'operation' => 'advertisement',
                'notes' => 'Create a paid advertise',
                'target_id' => null
            ];
            $transaction = $user->transactions()->create($transactionData);
            return response()->json(['status' => 200, 'message' => "Sponsor has been added successfully."]);
        }

    }


    public function update(PostSponsorFormRequest $request, Sponsor $sponsor)
    {
        $isUpdated = $sponsor->fill($request->validated())->update($request->validated());
        if ($isUpdated)
            return response()->json(['status' => 200, 'message' => "Sponsor has been updated successfully."]);
        else
            return response()->json(['status' => 400, 'message' => "Something was wrong."]);
    }


    public function delete(Sponsor $sponsor)
    {
        $isUpdated = $sponsor->delete();
        if ($isUpdated)
            return response()->json(['status' => 200, 'message' => "Sponsor has been deleted successfully."]);
        else
            return response()->json(['status' => 400, 'message' => "Something was wrong."]);
    }


    public function stop(Sponsor $sponsor)
    {

        $isStopped = $sponsor->update(['activated_at' => $sponsor->activated_at != null ? null : date('Y-m-d H:i:s')]);
        if ($isStopped)
            return response()->json(['status' => 200, 'message' => "Operation has been success."]);
        else
            return response()->json(['status' => 400, 'message' => "Something was wrong."]);
    }


    public function paidSponsorList(Request $request)
    {

        /**
         * Set Default Value For Skip Count To Avoid Error In Service.
         * @ Default Value 15...
         */
        if (isset($request->pageSize)):
            $pageSize = $request->pageSize;
        else:
            $pageSize = 10;
        endif;

        $sponsors = Sponsor::where('activated_at', '!=', null)->whereType('paid')->whereDate('expire_at', '>',Carbon::now())->inRandomOrder()->paginate($pageSize);
        return SponsorsIndexResource::collection($sponsors);

    }


    public function getFreeAds(Request $request)
    {

        // get token from header if user logged in else return an empty array.
        $token = ltrim($request->headers->get('Authorization'), "Bearer ");

        if ($token != "") :
            $collectionsIds = getTargetsAndFollowersBusiness($token);

            // get free ads to show in logged in user profile.
            $sponsors = Sponsor::where('activated_at', '!=', null)->whereIn('user_id', $collectionsIds)->whereType('free')->get();

            // return a list of sponsors free in account profile.
            return SponsorsIndexResource::collection($sponsors)->additional(['status' => 200, 'message' => "list of free ads."]);

        else:
            // get free ads to show in logged in user profile.
            $sponsors = Sponsor::where('activated_at', '!=', null)->whereType('free')->paginate(15);
            // return a list of sponsors free in account profile.
            return SponsorsIndexResource::collection($sponsors)->additional(['status' => 200, 'message' => "free ads."]);
        endif;


    }
}
