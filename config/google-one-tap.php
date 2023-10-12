<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Google Client ID
    |--------------------------------------------------------------------------
    |
    | This value is your Google app's client ID, which is found
    | and created in the Google Developers Console.
    |
    */

    'client_id' => env('GOOGLE_CLIENT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Google Client Secret
    |--------------------------------------------------------------------------
    */

    'client_secret' => env('GOOGLE_CLIENT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Google Callback Url
    |--------------------------------------------------------------------------
    */

    'call_back' => env('GOOGLE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Enable Google One Tap (string)
    |--------------------------------------------------------------------------
    |
    | This determines whether to display One tap or not. The default value is
    | true. Google One tap will not be displayed when this value is false
    |
    */

    'enable' => env('ENABLE_GOOGLE_ONE_TAP', "true"),

    /*
    |--------------------------------------------------------------------------
    | Custom Google One Tap
    |--------------------------------------------------------------------------
    */

    'custom' => []
];