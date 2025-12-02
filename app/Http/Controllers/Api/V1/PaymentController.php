<?php

namespace App\Http\Controllers\Api\V1;

use App\Libraries\Main;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{


    public $config;

    public function __construct(Main $config)
    {
        $this->config = $config;
    }

    public function store(Request $request)
    {

        if ($this->config->calculateUserBalance($request->user()) < $request->price)
            return response()->json(['message' => 'الرصيد غير كافي'], 400);

        $inputs = $request->all();


        /**
         * @ If coupon ID.
         */

        if (isset($request->code) && $request->code != "") {
            $inputs['coupon_id'] = $request->code;
            $inputs['code_type'] = $request->codeType;
        }


        if (isset($request->profileCode) && $request->profileCode != "") :
            $targetUser = User::whereCode($request->profileCode)->first();

            $month = 0;
            if ($subscription = $targetUser->subscriptions->where('is_active', 1)->first()) {
                $month = Carbon::parse($subscription->finished_at)->format('m') - Carbon::now()->format('m');
                $subscription->update(['is_active' => 0]);
            }


            $inputs['category_id'] = optional($targetUser->category)->parent_id;
            $inputs['finished_at'] = Carbon::now()->addMonths($request->duration + $month)->toDateTimeString();

            if ($targetUser->subscriptions()->create($inputs)) :

                $data = [
                    'status' => 'withdrawal',
                    'price' => $request->price,
                    'operation' => 'subscription',
                    'notes' => 'Subscription Another Account',
                    'target_id' => $targetUser->id
                ];

                $request->user()->transactions()->create($data);
                $targetUser->update(['paid_at' => Carbon::now()]);
                return response()->json([
                    'status' => 200
                ]);
            endif;

        else :

            $month = 0;
            if ($subscription = $request->user()->subscriptions->where('is_active', 1)->first()) {
                $month = Carbon::parse($subscription->finished_at)->format('m') - Carbon::now()->format('m');
                $subscription->update(['is_active' => 0]);
            }

            $categoryId = null;

            if ($request->user()->category)
                $categoryId = optional($request->user()->category)->parent_id;

            $inputs['category_id'] = $categoryId;
            $inputs['finished_at'] = Carbon::now()->addMonths($request->duration + $month)->toDateTimeString();

            if ($request->user()->subscriptions()->create($inputs)) :


                $request->user()->transactions()->create([
                    'status' => 'withdrawal',
                    'price' => $request->price,
                    'operation' => 'subscription',
                    'notes' => 'Subscription Account',
                    'target_id' => null
                ]);

                if (isset($request->codeType) && $request->codeType == "profileCode") {
                    $setting = new \App\Models\Setting;

                    $ownerCode = \App\Models\User::whereCode($request->code)->first();

                    if (!$ownerCode)
                        return response()->json([
                            'status' => 400,
                            'message' => "كود البروفايل المستخدم غير موجود"
                        ]);

                    if ($request->user()->code == $request->code) {
                        return response()->json([
                            'status' => 400,
                            'message' => "لا يمكنك إستخدام كود البروفايل الخاص بك"
                        ]);
                    }


                    if (isset($request->categoryId) && $request->categoryId != "") {
                        $category = Category::whereId($request->categoryId)->first();
                        $cost = $category->per_month;
                        if ($request->duration >= 12)
                            $cost = $category->per_year;
                    } else {
                        $cost = optional($request->user()->category)->parent->per_month;
                        if ($request->duration >= 12)
                            $cost = optional($request->user()->category)->parent->per_year;
                    }

                    $commissionMonths = $setting->getBody('commission_months');
                    if ($ownerCode->gifts != null)
                        $commissionMonths = $ownerCode->gifts->commission_months;

                    $costPerMonth = $cost;

                    if ($request->duration >= 12)
                        $costPerMonth = $cost / 12;

                    $ownerCodeCommission = $costPerMonth * $commissionMonths * ($request->duration / 12);


                    if (isset($request->code) && $request->code != "") :
                        $dataOwner = array(
                            'status' => 'deposit',
                            'price' => sprintf("%.2f", $ownerCodeCommission),
                            'operation' => 'award',
                            'notes' => 'From Registeration By Code Profile - ' . auth()->user()->code,
                            'target_id' => $request->user()->id
                        );
                        $ownerCode->transactions()->create($dataOwner);
                    endif;
                }
                return response()->json([
                    'status' => 200
                ]);
            endif;
        endif;
    }

    //
    //    public function chargeAccount(Request $request)
    //    {
    //        $data = array(
    //            'status' => 'deposit',
    //            'price' => $request->price,
    //            'operation' => 'recharge',
    //            'notes' => 'Charge Account',
    //            'target_id' => null
    //        );
    //        $request->user()->transactions()->create($data);
    //
    //        return response()->json([
    //            'status' => 200
    //        ]);
    //    }

    public function transferToAnother(Request $request)
    {

        $user = User::whereCode($request->profileCode)->first();
        if ($this->config->calculateUserBalance($request->user()) < $request->price)
            return response()->json(['message' => 'الرصيد غير كافي'], 400);


        $toUser = [
            'status' => 'deposit',
            'price' => $request->price,
            'operation' => 'transfer',
            'notes' => 'Receiver Transfer From Another Account',
            'target_id' => $request->user()->id
        ];
        $user->transactions()->create($toUser);

        $data = [
            'status' => 'withdrawal',
            'price' => $request->price,
            'operation' => 'transfer',
            'notes' => 'Transfer To Another Account',
            'target_id' => $user->id
        ];

        if ($request->user()->transactions()->create($data))
            return response()->json([
                'status' => 200,
            ]);
    }


    public function fawrySuccessPayment(Request $request)
    {
        print_r($request->input());
        //Log::notice($request->input());
        Log::notice("Fawry call");
        // $main = new \App\Libraries\Main();
        // $chargeResponse = json_decode($request->chargeResponse, true);
        // $request->merchantRefNumber;
        // $request->referenceNumber;
        // $request->paymentMethod;


        if(!$request->merchantRefNumber)
        {
            Log::notice("No Ref number provided!");
            return response()->json([
                'status' => 400,
                "error" => "No Ref number provided!"
            ], 400);
        }
        Log::notice("merchantRefNumber: ". $request->merchantRefNumber);
        
        $payment = Payment::whereId($request->merchantRefNumber)->first();
        if(!$payment)
        {
            Log::notice("Order not found!");
            return response()->json([
                'status' => 400,
                "error" => "Order not found!"
            ], 400);
        }

        if($payment->paid_at)
        {
            Log::notice("Order already paid!");
            return response()->json([
                'status' => 400,
                "error" => "Order already paid!"
            ], 400);
        }
        
        if (strtoupper($request->paymentMethod) == "PAYATFAWRY") {
            Log::notice("PAYATFAWRY");
            $paid = $this->paymentAtFawry($request, $payment);
        } else {
            Log::notice("Card Pay");
            $paid = $this->paymentByCard($payment, $request);
        }

        if($paid)
            return response()->json([
                'status' => 200,
            ], 200);

        //
        //        $userId = $payment->user->id;
        //        $price = $request->price;
        //        $code = $request->code;
        //        $codeType = $request->codeType;
        //        $duration = $request->duration;
        //        $serviceName = $request->actionType != "" ? 'recharge' : 'subscription';
        //        $paymentData = array(
        //            'price' => $price,
        //            'payment_type' => $chargeResponse['paymentMethod'],
        //            'payment_no' => $chargeResponse['referenceNumber'],
        //            'operation_type' => $serviceName,
        //            'paid_at' => $chargeResponse['paymentMethod'] != "PAYATFAWRY" ? Carbon::now() : null,
        //
        //        );
        //
        //        $user = \App\Models\User::whereId($userId)->first();
        //        $payment = $user->payments()->create($paymentData);
        //
        //        if ($serviceName != "recharge"):
        //
        //
        //            $month = 0;
        //            if ($subscription = $user->subscriptions->where('is_active', 1)->first()) {
        //                $month = Carbon::parse($subscription->finished_at)->format('m') - Carbon::now()->format('m');
        //                $subscription->update(['is_active' => 0]);
        //            }
        //
        //            $freeMonths = 0;
        //            if ($codeType != "" && $codeType == 'profileCode')
        //                $freeMonths = giftsAndMonthsAfterRegistration($code, $userId, $duration);
        //            $totalMonths = $freeMonths + $month;
        //
        //            $inputs['category_id'] = optional($user->category)->parent_id;
        //            $inputs['finished_at'] = Carbon::now()->addMonths($duration + $totalMonths)->toDateTimeString();
        //            $inputs['user_id'] = $userId;
        //            $inputs['price'] = $price;
        //            $inputs['duration'] = $duration;
        //            $inputs['coupon_id'] = $codeType == 'couponCode' ? $code : null;
        //            if ($newSubscription = $user->subscriptions()->create($inputs)):
        //                $payment->update(['operation_id' => $newSubscription->id]);
        //                return response()->json(['status' => 200]);
        //            endif;
        //
        //        else:
        //
        //            if ($chargeResponse['paymentMethod'] != "PAYATFAWRY") {
        //                $transactionData = [
        //                    'status' => 'deposit',
        //                    'price' => $price,
        //                    'operation' => 'recharge',
        //                    'notes' => 'Charge Account by fawryPayment.',
        //                    'target_id' => null
        //                ];
        //                $transaction = $user->transactions()->create($transactionData);
        //                $payment->update(['operation_id' => $transaction->id]);
        //                return response()->json(['status' => 200]);
        //            }
        //
        //
        //        endif;
    }


    private function paymentByCard(Payment $payment, $response)
    {
        // https://businessinmap.com/testing/api/v1/fawry-success-payment
        // ?type=ChargeResponse&
        // referenceNumber=7105189834
        // &merchantRefNumber=63445a857b1f863445a857b1f963445a857b1fa
        // &orderAmount=111
        // &paymentAmount=111
        // &fawryFees=0
        // &orderStatus=PAID
        // &paymentMethod=PayUsingCC
        // &paymentTime=1665424045970
        // &customerName=
        // &customerMobile=01093121766
        // &customerMail=businessinmap2019%40gmail.com
        // &customerProfileId=183
        // &signature=4ceca1a1d5e2dfd8af2ac19a6aa69a727c195c32205161652b39b0b11ccc42ae
        // &taxes=0
        // &statusCode=200
        // &statusDescription=Operation%20done%20successfully
        // &basketPayment=false
        if ($payment->operation_type == "recharge") {
            $transactionData = [
                'status' => 'deposit',
                'price' => $payment->price,
                'operation' => 'recharge',
                'notes' => 'Charge Account by Fawry (ATFAWRY).',
                'target_id' => null
            ];
            $transaction = $payment->user->transactions()->create($transactionData);

            $paymentData = array(
                'payment_type' => strtoupper($response['paymentMethod']),
                'payment_no' => $response['referenceNumber'],
                'operation_id' => $transaction->id,
                'paid_at' => Carbon::now()
            );

            $payment->update($paymentData);

            return response()->json(['status' => 200, 'message' => "Transaction paid success."]);
        } else {
            $currentSubscription = Subscription::whereId($payment->operation_id)->first();
            $month = 0;
            if ($subscription = $payment->user->subscriptions->where('is_active', 1)->first()) {
                $month = Carbon::parse($subscription->finished_at)->format('m') - Carbon::now()->format('m');
                $subscription->update(['is_active' => 0]);
            }

            //            $freeMonths = 0;
            //            if ($currentSubscription->code_type != "" && $currentSubscription->code_type == 'profileCode')
            //
            //                $freeMonths = giftsAndMonthsAfterRegistration($currentSubscription->code, $payment->user->id, $currentSubscription->duration);
            //            $totalMonths = $freeMonths + $month;


            if ($currentSubscription->code_type != "" && $currentSubscription->code_type == 'profileCode') {


                $setting = new \App\Models\Setting;

                $ownerCode = \App\Models\User::whereCode($currentSubscription->coupon_id)->first();

                if ($ownerCode && $currentSubscription->user->code != $currentSubscription->coupon_id) {
                    $cost = optional($currentSubscription->user->category)->parent->per_month;
                    if ($currentSubscription->duration >= 12)
                        $cost = optional($currentSubscription->user->category)->parent->per_year;

                    $commissionMonths = $setting->getBody('commission_months');

                    if ($ownerCode->gifts != null)
                        $commissionMonths = $ownerCode->gifts->commission_months;


                    $costPerMonth = $cost;

                    if ($currentSubscription->duration >= 12)
                        $costPerMonth = $cost / 12;

                    $ownerCodeCommission = $costPerMonth * $commissionMonths * ($currentSubscription->duration / 12);

                    $dataOwner = array(
                        'status' => 'deposit',
                        'price' => sprintf("%.2f", $ownerCodeCommission),
                        'operation' => 'award',
                        'notes' => 'From Subscription By Code Profile - ' . $currentSubscription->user->code,
                        'target_id' => $currentSubscription->user_id
                    );
                    $ownerCode->transactions()->create($dataOwner);
                }
            }

            $currentSubscription->finished_at = Carbon::now()->addMonths($currentSubscription->duration + $month)->toDateTimeString();
            $currentSubscription->is_active = 1;

            if ($currentSubscription->save()) {
                $payment->update(['paid_at' => Carbon::now(), 'payment_type' => strtoupper($response['paymentMethod']), 'payment_no' => $response['referenceNumber']]);
                return response()->json(['status' => 200]);
            }
        }
    }

    private function paymentAtFawry($response, Payment $payment)
    {

        if (!$this->checkIfFawryNoIsGenerated($response['referenceNumber'])) {

            $paymentData = array(
                'payment_type' => strtoupper($response['paymentMethod']),
                'payment_no' => $response['referenceNumber'],
                'paid_at' => null,
            );
            if ($payment->update($paymentData))
                return;
        } else {


            if ($payment->operation_type == "recharge") {
                $transactionData = [
                    'status' => 'deposit',
                    'price' => $payment->price,
                    'operation' => 'recharge',
                    'notes' => 'Charge Account by Fawry (ATFAWRY).',
                    'target_id' => null
                ];
                $transaction = $payment->user->transactions()->create($transactionData);
                $payment->update(['operation_id' => $transaction->id, 'paid_at' => Carbon::now()]);
                // dd($response);
                // return 'Please Close This window';
                // return response()->json(['status' => 200, 'message' => "Transaction paid success.", ]);
            } else {

                $currentSubscription = Subscription::whereId($payment->operation_id)->first();
                $month = 0;
                if ($subscription = $payment->user->subscriptions->where('is_active', 1)->first()) {
                    $month = Carbon::parse($subscription->finished_at)->format('m') - Carbon::now()->format('m');
                    $subscription->update(['is_active' => 0]);
                }

                //                $freeMonths = 0;
                //                if ($currentSubscription->code_type != "" && $currentSubscription->code_type == 'profileCode')
                //                    $freeMonths = giftsAndMonthsAfterRegistration($currentSubscription->code, $payment->user->id, $currentSubscription->duration);
                //                $totalMonths = $freeMonths + $month;

                $currentSubscription->finished_at = Carbon::now()->addMonths($currentSubscription->duration + $month)->toDateTimeString();
                $currentSubscription->is_active = 1;

                if ($currentSubscription->save()) {
                    $payment->update(['paid_at' => Carbon::now()]);
                    return "Saved";
                }
            }
        }
    }


    private function checkIfFawryNoIsGenerated($paymentNo)
    {

        $payment = Payment::wherePaymentNo($paymentNo)->first();

        return !$payment ? false : true;
    }
}
