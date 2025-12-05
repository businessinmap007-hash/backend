<?php

namespace App\Http\Controllers\Api\V1;

use App\Company;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Controller for notification actions (delete, mark as read, etc.)
 * تم فصل دوال التعديل/الحذف هنا عن دوال العرض لكسر مسؤوليات الكنترول
 */
class NotificationActionsController extends Controller
{
    public function __construct(Request $request)
    {
        $language = $request->headers->get('lang') ? $request->headers->get('lang') : 'ar';
        app()->setLocale($language);
    }

    /**
     * حذف إشعار للمستخدم
     * Request params:
     * - api_token: توكن المستخدم أو يمكنك تعديل لتأخذ Authorization header
     * - notifyId: معرف الإشعار للحذف
     */
    public function delete(Request $request)
    {
        $user = User::whereApiToken($request->api_token)->first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => __('trans.user_not_found')], 404);
        }

        $is_deleted = $user->notifications()->where('id', $request->notifyId)->delete();

        if ($is_deleted) {
            return response()->json([
                'status' => true,
                'count' => $user->unreadNotifications->count()
            ]);
        }

        return response()->json(['status' => false]);
    }

    /**
     * مساعدة: جلب اسم الشركة بالمعرف
     */
    public function getCompanyNameByID($id)
    {
        $company = Company::whereId($id)->first();
        return $company ? $company->name : null;
    }
}
