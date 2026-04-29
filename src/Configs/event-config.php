<?php

use twa\smsautils\Classes\CreateClientPortalNotification;
use twa\smsautils\Classes\ExceptionCase;
use twa\smsautils\Classes\HoldShipment;
use twa\smsautils\Classes\RTS;
use twa\smsautils\Classes\SendWP;
use twa\smsautils\Classes\SendSMS;
use twa\smsautils\Classes\SendOTP;
use twa\smsautils\Classes\SendEmail;
use twa\smsautils\Classes\SendWebhook;
use twa\smsautils\Classes\ExceptionOnPendingBeyondDefinedTime;

return [



    // 'a1b2c3d4-e5f6-4789-a012-b3c4d5e6f789' => SendWP::class,

    'b2c3d4e5-f6a7-4890-b123-c4d5e6f7a890' => SendSMS::class,

    // 'c3d4e5f6-g7h8-4901-b234-c5d6e7f8g901' => SendOTP::class,

    'd4e5f6g7-h8i9-4a01-b234-c5d6e7f8g901' => SendEmail::class,

    'e5f6g7h8-i9j0-4b12-c345-d6e7f8g9h012' => SendWebhook::class,
    'f6g7h8i9-j0k1-4c23-d456-e7f8g9h0i123' => CreateClientPortalNotification::class,
    'g7h8i9j0-k1l2-4d34-e567-f8g9h0i1j234' => HoldShipment::class,
    'h8i9j0k1-l2m3-4e45-f678-g9h0i1j2k345' => RTS::class,
    'i9j0k1l2-m3n4-4f56-g789-h0i1j2k3l456' => ExceptionCase::class,
    'j0k1l2m3-n4o5-4g67-h890-i1j2k3l4m567' => ExceptionOnPendingBeyondDefinedTime::class,
];
