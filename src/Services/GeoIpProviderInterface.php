<?php

namespace Subarist\WaterholeGeoIp\Services;

interface GeoIpProviderInterface
{
    public function countryCode(string $ip): ?string;
}