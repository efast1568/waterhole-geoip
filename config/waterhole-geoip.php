<?php

return [
    /*
    |--------------------------------------------------------------------------
    | GeoIP Provider
    |--------------------------------------------------------------------------
    |
    | If IPINFO_TOKEN is not configured, use IP-API Free.
    | Set IPINFO_TOKEN to use IPinfo instead.
    |
    */

    'provider' => env(
        'GEOIP_PROVIDER',
        env('IPINFO_TOKEN') ? 'ipinfo' : 'ip-api',
    ),

    'ip-api' => [
        'url' => 'http://ip-api.com/json',
    ],

    'ipinfo' => [
        'token' => env('IPINFO_TOKEN'),
    ],
];
