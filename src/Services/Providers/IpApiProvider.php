<?php

namespace Subarist\WaterholeGeoIp\Services\Providers;

use Illuminate\Support\Facades\Http;
use Subarist\WaterholeGeoIp\Services\GeoIpProviderInterface;

class IpApiProvider implements GeoIpProviderInterface
{
    public function countryCode(string $ip): ?string
    {
        try {
            $response = Http::timeout(3)
                ->get("http://ip-api.com/json/{$ip}", [
                    'fields' => 'status,countryCode',
                ]);

            if (! $response->successful()) {
                return null;
            }

            if ($response->json('status') !== 'success') {
                return null;
            }

            $countryCode = $response->json('countryCode');

            return is_string($countryCode) && strlen($countryCode) === 2
                ? strtoupper($countryCode)
                : null;
        } catch (\Throwable) {
            return null;
        }
    }
}