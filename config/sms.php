<?php
return [
    /*
    |--------------------------------------------------------------------------
    | SMS Driver
    |--------------------------------------------------------------------------
    | Supported: "africastalking", "twilio", "log"
    | Use "log" for local development — messages go to Laravel's log file.
    */
    'driver' => env('SMS_DRIVER', 'log'),

    'africastalking' => [
        'username'  => env('SMS_USERNAME'),
        'api_key'   => env('SMS_API_KEY'),
        'sender_id' => env('SMS_SENDER_ID', ''),
    ],

    'twilio' => [
        'sid'   => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'from'  => env('TWILIO_FROM'),
    ],
];
