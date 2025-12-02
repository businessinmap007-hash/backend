<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class DeliveryController extends Controller
{
    public function __construct()
    {
        $language = request()->headers->get('lang') ?: 'ar';
        app()->setLocale($language);
    }

    /**
     * 1) إنشاء طلب دليفري جديد
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id'     => 'required|exists:users,id',
            'pickup_address'  => 'required|string',
            'pickup_lat'      => 'required|numeric',
            'pickup_lng'      => 'required|numeric',
            'dropoff_address' => 'required|string',
            'dropoff_lat'     => 'required|numeric',
            'dropoff_lng'     => 'required|numeric',
            'delivery_type'   => 'nullable|string|max:191',
            'weight'          => 'nullable|string|max:191',
            'price_estimated' => 'nullable|numeric|min:0',
            'price_final'     => 'nullable|numeric|min:0',
            'payment_method'  => 'required|in:cash,online,wallet',
            'notes'           => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        $order = DeliveryOrder::create([
            'user_id'         => $user->id,
            'business_id'     => $request->business_id,
            'pickup_address'  => $request->pickup_address,
            'pickup_lat'      => $request->pickup_lat,
            'pickup_lng'      => $request->pickup_lng,
            'dropoff_address' => $request->dropoff_address,
            'dropoff_lat'     => $request->dropoff_lat,
            'dropoff_lng'     => $request->dropoff_lng,
            'delivery_type'   => $request->delivery_type,
            'weight'          => $request->weight,
            'price_estimated' => $request->price_estimated,
            'price_final'     => $request->price_final,
            'payment_method'  => $request->payment_method,
            'price'           => $request->price_estimated ?? $request->price_final,
            'notes'           => $request->notes,
            'status'          => 'pending',
        ]);

        // إشعار للبزنس
        if (function_exists('send_notification')) {
            send_notification(
                $order->business_id,
                "طلب دليفري جديد",
                "delivery_new",
                [
                    "delivery_order_id" => $order->id,
                    "from_user_id"      => $order->user_id,
                ]
            );
        }

        return response()->json([
            'status'  => 200,
            'message' => 'Delivery order created successfully',
            'data'    => $order,
        ]);
    }

    /**
     * 2) طلبات المستخدم (العميل)
     */
    public function myOrders(Request $request)
    {
        $orders = DeliveryOrder::where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'status'  => 200,
            'message' => 'My delivery orders',
            'data'    => $orders,
        ]);
    }

    /**
     * 3) طلبات البزنس
     */
    public function businessOrders(Request $request)
    {
        $orders = DeliveryOrder::where('business_id', $request->user()->id)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'status'  => 200,
            'message' => 'Business delivery orders',
            'data'    => $orders,
        ]);
    }

    /**
     * 4) طلبات السائق
     */
    public function driverOrders(Request $request)
    {
        $orders = DeliveryOrder::where('courier_id', $request->user()->id)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'status'  => 200,
            'message' => 'Driver delivery orders',
            'data'    => $orders,
        ]);
    }

    /**
     * 5) تفاصيل الطلب
     */
        public function show($id)
    {
        $order = DeliveryOrder::with(['user', 'business', 'driver'])->findOrFail($id);

        // جلب موقع السائق إن وجد
        $driverLocation = null;
        if ($order->courier_id) {           // لو عندك الاسم driver_id غيّر هنا
            $driverLocation = DB::table('driver_locations')
                ->where('courier_id', $order->courier_id)   // أو driver_id
                ->first();
        }

        return response()->json([
            'status'          => 200,
            'message'         => 'Delivery order details',
            'data'            => $order,
            'driver_location' => $driverLocation,
        ]);
    }


    

    
    /**
     * تحديث موقع السائق
     */ 

    public function updateDriverLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $driver = $request->user();

        \DB::table('driver_locations')->updateOrInsert(
            ['courier_id' => $driver->id],
            [
                'lat' => $request->lat,
                'lng' => $request->lng,
                'updated_at' => now()
            ]
        );

        return response()->json([
            'status'  => 200,
            'message' => 'Driver location updated'
        ]);
    }


    /**
     * 6) السائق يقبل الطلب
     */
    public function accept(Request $request, $id)
    {
        $order = DeliveryOrder::findOrFail($id);

        if ($order->status !== 'pending') {
            return response()->json([
                'status'  => 400,
                'message' => 'Order is not available for accepting',
            ], 400);
        }

        $order->update([
            'courier_id' => $request->user()->id,
            'status'     => 'accepted',
        ]);

        // إشعار للعميل
        if (function_exists('send_notification')) {
            send_notification(
                $order->user_id,
                "تم قبول طلب الدليفري",
                "delivery_accepted",
                [
                    "delivery_order_id" => $order->id,
                    "courier_id"        => $order->courier_id,
                ]
            );
        }

        return response()->json([
            'status'  => 200,
            'message' => 'Order accepted successfully',
            'data'    => $order,
        ]);
    }

    /**
     * 7) تحديث حالة الطلب
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,accepted,on_the_way,delivering,delivered,cancelled_by_user,cancelled_by_business,cancelled_by_driver',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $order = DeliveryOrder::findOrFail($id);

        $order->update([
            'status' => $request->status,
        ]);

        // إشعارات
        if (function_exists('send_notification')) {
            foreach (array_unique([$order->user_id, $order->business_id, $order->courier_id]) as $target) {
                if ($target) {
                    send_notification(
                        $target,
                        "تم تحديث حالة الدليفري إلى {$request->status}",
                        "delivery_status",
                        [
                            "delivery_order_id" => $order->id,
                            "status"            => $request->status,
                        ]
                    );
                }
            }
        }

        return response()->json([
            'status'  => 200,
            'message' => 'Order status updated',
            'data'    => $order,
        ]);
    }



    /**
     * 8) إلغاء الطلب
     */
    public function cancel(Request $request, $id)
    {
        $order = DeliveryOrder::findOrFail($id);
        $user  = $request->user();

        if ($user->id == $order->user_id) {
            $status = 'cancelled_by_user';
        } elseif ($user->id == $order->business_id) {
            $status = 'cancelled_by_business';
        } elseif ($user->id == $order->courier_id) {
            $status = 'cancelled_by_driver';
        } else {
            return response()->json([
                'status'  => 403,
                'message' => 'Unauthorized',
            ], 403);
        }

        $order->update(['status' => $status]);

        // إشعار للأطراف
        if (function_exists('send_notification')) {
            foreach ([$order->user_id, $order->business_id, $order->courier_id] as $target) {
                if ($target && $target != $user->id) {
                    send_notification(
                        $target,
                        "تم إلغاء طلب الدليفري",
                        "delivery_cancelled",
                        [
                            "delivery_order_id" => $order->id,
                            "cancelled_by"      => $user->id,
                        ]
                    );
                }
            }
        }

        return response()->json([
            'status'  => 200,
            'message' => 'Order cancelled successfully',
            'data'    => $order,
        ]);
    }

    /**
     * 9) السائق في الطريق (Tracking Notification)
     */
    public function driverOnTheWay(Request $request, $id)
    {
        $order = DeliveryOrder::findOrFail($id);
        $driver = $request->user();

        if (function_exists('send_notification')) {
            send_notification(
                $order->user_id,
                "السائق في الطريق إليك",
                "driver_tracking",
                [
                    "driver_id" => $driver->id,
                    "order_id"  => $order->id,
                    "track_url" => "/driver/location/" . $driver->id
                ]
            );
        }

        return response()->json([
            'status'  => 200,
            'message' => 'Driver tracking notification sent',
        ]);
    }
}
