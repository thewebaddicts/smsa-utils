<?php

return [



    'a1b2c3d4-e5f6-4789-a012-b3c4d5e6f789' => \App\Classes\SendWP::class,

    'b2c3d4e5-f6a7-4890-b123-c4d5e6f7a890' => \App\Classes\SendSMS::class,

    'c3d4e5f6-g7h8-4901-b234-c5d6e7f8g901' => \App\Classes\SendOTP::class,

    'd4e5f6g7-h8i9-4a01-b234-c5d6e7f8g901' => \App\Classes\SendEmail::class,

    'e5f6g7h8-i9j0-4b12-c345-d6e7f8g9h012' => \App\Classes\SendWebhook::class,
];
