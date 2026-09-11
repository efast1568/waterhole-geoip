<?php

namespace Subarist\WaterholeGeoIp\Services\Providers;

use Illuminate\Support\Facades\Http;
use Subarist\WaterholeGeoIp\Services\GeoIpProviderInterface;

class IpInfoProvider implements GeoIpProviderInterface
{
    public function countryCode(string $ip): ?string
    {
        $token = config('waterhole-geoip.ipinfo.token');

        if (! $token) {
            return null;
        }

        try {
            $response = Http::timeout(3)
                ->get("https://api.ipinfo.io/lite/{$ip}", [
                    'token' => $token,
                ]);

            if (! $response->successful()) {
                return null;
            }

            $countryCode = $response->json('country_code');

            return is_string($countryCode) && strlen($countryCode) === 2
                ? strtoupper($countryCode)
                : null;
        } catch (\Throwable) {
            return null;
        }
    }
}