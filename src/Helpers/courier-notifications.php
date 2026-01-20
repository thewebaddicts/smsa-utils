<?php

use twa\smsautils\Http\Controllers\OneSignalController;


if (!function_exists('send_notification_helper')) {
    /**
     * Base helper function to send notifications with multilingual support
     * 
     * @param string $title_en English title
     * @param string $title_ar Arabic title
     * @param string $message_en English message
     * @param string $message_ar Arabic message
     * @param array $conditions Conditions for targeting users
     * @param array|null $data Additional data to send
     * @param string|null $image_url Image URL
     * @param string|null $playerID Player ID
     * @return void
     */
    function send_notification_helper($title_en, $title_ar, $message_en, $message_ar, $conditions = [], $data = null, $image_url = null, $playerID = null)
    {
        $config = config('omnipush.onesignal');
        (new OneSignalController($config['data']))->sendPush(
            ["en" => $title_en, "ar" => $title_ar],
            ["en" => $message_en, "ar" => $message_ar],
            $conditions,
            $data,
            $image_url,
            $playerID
        );
    }
}

if (!function_exists('notify_new_pickup_request')) {
    /**
     * Send notification for new pickup request
     * 
     * Example:
     * notify_new_pickup_request($courier->id);
     * notify_new_pickup_request($courier->id, ['pickup_id' => 123]);
     * 
     * @param int $courier_id The courier user ID
     * @param array|null $data Additional data
     * @return void
     */
    function notify_new_pickup_request($courier_id, $pickup_id, $data = null)
    {
        $title_en = "New Pickup Request";
        $title_ar = "طلب استلام جديد";
        $message_en = "You have a new pickup request";
        $message_ar = "لديك طلب استلام جديد";

        send_notification_helper(
            $title_en,
            $title_ar,
            $message_en,
            $message_ar,
            ["condition" => ["user_id"], "value" => [$courier_id]],
            $pickup_id,
            $data
        );
    }
}

if (!function_exists('notify_new_delivery_trip')) {
    /**
     * Send notification for new trip
     * 
     * Example:
     * notify_new_trip($courier->id);
     * notify_new_trip($courier->id, ['trip_id' => 456, 'route' => 'Route A']);
     * 
     * @param int $courier_id The courier user ID
     * @param array|null $data Additional data
     * @return void
     */
    function notify_new_delivery_trip($courier_id, $data = null)
    {
        $title_en = "New Delivery Trip";
        $title_ar = "رحلة تسليم جديدة";
        $message_en = "You have a new delivery trip";
        $message_ar = "لديك رحلة تسليم جديدة";

        send_notification_helper(
            $title_en,
            $title_ar,
            $message_en,
            $message_ar,
            ["condition" => ["user_id"], "value" => [$courier_id]],
            $data
        );
    }
}



if (!function_exists('notify_shipment_delivered')) {
    /**
     * Send notification for successful delivery
     * 
     * Example:
     * notify_shipment_delivered($customer->id);
     * 
     * @param int $user_id The user ID
     * @param array|null $data Additional data
     * @return void
     */
    function notify_shipment_delivered($courier_id, $awb_id, $data = null)
    {
        $title_en = "Shipment Delivered";
        $title_ar = "تم تسليم الشحنة";
        $message_en = "Your shipment has been delivered successfully";
        $message_ar = "تم تسليم شحنتك بنجاح";

        send_notification_helper(
            $title_en,
            $title_ar,
            $message_en,
            $message_ar,
            ["condition" => ["user_id"], "value" => [$courier_id]],
            $awb_id,
            $data
        );
    }
}

if (!function_exists('notify_pickup_completed')) {
    /**
     * Send notification when pickup is completed
     * 
     * Example:
     * notify_pickup_completed($courier->id, ['pickup_id' => 123]);
     * 
     * @param int $courier_id The courier user ID
     * @param array|null $data Additional data
     * @return void
     */
    function notify_pickup_completed($courier_id, $data = null)
    {
        $title_en = "Pickup Completed";
        $title_ar = "تم الاستلام";
        $message_en = "Pickup has been completed successfully";
        $message_ar = "تم الاستلام بنجاح";

        send_notification_helper(
            $title_en,
            $title_ar,
            $message_en,
            $message_ar,
            ["condition" => ["user_id"], "value" => [$courier_id]],
            $data
        );
    }
}

if (!function_exists('notify_general_alert')) {
    /**
     * Send a general notification/alert
     * 
     * Example:
     * notify_general_alert($user->id, ['message' => 'Important update']);
     * 
     * @param int $user_id The user ID
     * @param array|null $data Additional data
     * @return void
     */
    function notify_general_alert($user_id, $data = null)
    {
        $title_en = "Alert";
        $title_ar = "تنبيه";
        $message_en = "You have a new notification";
        $message_ar = "لديك إشعار جديد";

        send_notification_helper(
            $title_en,
            $title_ar,
            $message_en,
            $message_ar,
            ["condition" => ["user_id"], "value" => [$user_id]],
            $data
        );
    }
}

if (!function_exists('mission_transfer_alert')) {
    function mission_transfer_alert($courier_id, $data = null)
    {
        $title_en = "Mission Transfer";
        $title_ar = "تحويل مهمة";
        $message_en = "You have a new mission transfer";
        $message_ar = "لديك تحويل مهمة جديد";

        send_notification_helper(
            $title_en,
            $title_ar,
            $message_en,
            $message_ar,
            ["condition" => ["user_id"], "value" => [$courier_id]],
            $data
        );
    }
}
if (!function_exists('mission_transfer_request_received')) {
    function mission_transfer_request_received($courier_id, $data = null)
    {
        $title_en = "Mission Transfer Request Received we will notify you when if it's approved";
        $title_ar = "ويتم ارسال طلب تحويل مهمة وسنخطرك عندما يتم الموافقة عليه";
        $message_en = "You have a new mission transfer request we will notify you when if it's approved";
        $message_ar = "لديك طلب تحويل مهمة جديد وسنخطرك عندما يتم الموافقة عليه";


        send_notification_helper(
            $title_en,
            $title_ar,
            $message_en,
            $message_ar,
            ["condition" => ["user_id"], "value" => [$courier_id]],
            $data
        );
    }
}

if (!function_exists('mission_transfer_request_approved')) {

    function mission_transfer_request_approved($courier_id, $data = null)
    {
        $title_en = "Mission Transfer Request Approved";
        $title_ar = "تم الموافقة على طلب تحويل مهمة";
        $message_en = "Your mission transfer request has been approved";
        $message_ar = "تم الموافقة على طلب تحويل مهمتك";



        send_notification_helper(
            $title_en,
            $title_ar,
            $message_en,
            $message_ar,
            ["condition" => ["user_id"], "value" => [$courier_id]],
            $data
        );
    }
}
if (!function_exists('mission_transfer_request_rejected')) {
    function mission_transfer_request_rejected($courier_id, $data = null)
    {
        $title_en = "Mission Transfer Request Rejected";
        $title_ar = "تم رفض طلب تحويل مهمة";
        $message_en = "Your mission transfer request has been rejected";
        $message_ar = "تم رفض طلب تحويل مهمتك";

        send_notification_helper(
            $title_en,
            $title_ar,
            $message_en,
            $message_ar,
            ["condition" => ["user_id"], "value" => [$courier_id]],
            $data
        );
    }
}
if (!function_exists('mission_transfer_request_completed')) {
    function mission_transfer_request_completed($courier_id, $data = null)
    {
        $title_en = "Mission Transfer Request Completed";
        $title_ar = "تم إكمال طلب تحويل مهمة";
        $message_en = "Your mission transfer request has been completed";
        $message_ar = "تم إكمال طلب تحويل مهمتك";

        send_notification_helper(
            $title_en,
            $title_ar,
            $message_en,
            $message_ar,
            ["condition" => ["user_id"], "value" => [$courier_id]],
            $data
        );
    }
}

if (!function_exists('assign_courier_to_runsheet')) {
    function assign_courier_to_runsheet($courier_id, $data = null)
    {
        $title_en = "Assign Courier to Runsheet";
        $title_ar = "تم تعيين موظف للرحلة";
        $message_en = "You have a new runsheet assigned to you";
        $message_ar = "لديك رحلة جديدة";


        send_notification_helper(
            $title_en,
            $title_ar,
            $message_en,
            $message_ar,
            ["condition" => ["user_id"], "value" => [$courier_id]],
            $data
        );
    }
}

if (!function_exists('unassign_courier_from_pickup')) {
    function unassign_courier_from_pickup($courier_id, $pickup_id, $data = null)
    {
        $title_en = "Unassign Courier from Pickup" . $pickup_id;
        $title_ar = "تم إلغاء تعيين موظف للاستلام" . $pickup_id;
        $message_en = "You have been unassigned from the pickup" . $pickup_id;
        $message_ar = "تم إلغاء تعيينك من الاستلام" . $pickup_id;

        send_notification_helper(
            $title_en,
            $title_ar,
            $message_en,
            $message_ar,
            ["condition" => ["user_id"], "value" => [$courier_id]],
            $data
        );
    }
}
if (!function_exists('notify_courier_change_address')) {
    function notify_courier_change_address($courier_id, $awb, $data = null)
    {
        $title_en = "Change Address Alert for shipment " . $awb;
        $title_ar = "تنبيه تغيير العنوان للشحنة " . $awb;
        $message_en = "You have a new address for shipment $awb";
        $message_ar = "لديك تغيير عنوان جديد للشحنة $awb";

        send_notification_helper(
            $title_en,
            $title_ar,
            $message_en,
            $message_ar,
            ["condition" => ["user_id"], "value" => [$courier_id]],
            $data
        );
    }
}
if (!function_exists('notify_courier_change_route')) {
    function notify_courier_change_route($courier_id, $awb, $data = null)
    {
        $title_en = "Change Route Alert for shipment " . $awb;
        $title_ar = "تنبيه تغيير الرحلة للشحنة " . $awb;
        $message_en = "You have a new route for shipment $awb";
        $message_ar = "لديك تغيير رحلة جديد للشحنة $awb";

        send_notification_helper(
            $title_en,
            $title_ar,
            $message_en,
            $message_ar,
            ["condition" => ["user_id"], "value" => [$courier_id]],
            $data
        );
    }
}

