<?php

namespace Subarist\WaterholeGeoIp\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Subarist\WaterholeGeoIp\Models\ContentIp;
use Subarist\WaterholeGeoIp\Models\IpInfo;
use Subarist\WaterholeGeoIp\Services\GeoIpService;

class ProcessGeoIp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $contentType,
        public int $contentId,
        public string $ip
    ) {}

    public function handle(GeoIpService $geoIpService): void
    {
        $contentIp = ContentIp::firstOrCreate(
            ['content_type' => $this->contentType, 'content_id' => $this->contentId],
            ['ip_address' => $this->ip]
        );

        if (!$contentIp->ip_address) {
            return;
        }

        if (IpInfo::where('ip_address', $this->ip)->exists()) {
            return;
        }

        $countryCode = $geoIpService->countryCode($this->ip);
        if ($countryCode) {
            IpInfo::create([
                'ip_address' => $this->ip,
                'country_code' => strtoupper($countryCode),
            ]);
        }
    }
}