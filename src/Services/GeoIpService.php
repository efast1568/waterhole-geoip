<?php

namespace Subarist\WaterholeGeoIp\Services;

class GeoIpService
{
    public function __construct(
        protected GeoIpProviderInterface $provider,
    ) {
    }

    public function countryCode(?string $ip): ?string
    {
        if (! $ip) {
            return null;
        }

        return $this->provider->countryCode($ip);
    }
}