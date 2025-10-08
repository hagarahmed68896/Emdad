<?php

return [

    'secret' => env('NOCAPTCHA_SECRET'),
    'sitekey' => env('NOCAPTCHA_SITEKEY'),

    'options' => [
        'verify' => 'C:\php-8.4.6\extras\ssl\cacert.pem',
        'curl' => [
            CURLOPT_CAINFO => 'C:\php-8.4.6\extras\ssl\cacert.pem',
        ],
    ],

];
