<?php

namespace Subarist\WaterholeGeoIp\Services;

use Subarist\WaterholeGeoIp\Models\ContentIp;
use Subarist\WaterholeGeoIp\Models\IpInfo;

class GeoIpFlagService
{
    /**
     * 根據 attribution 的 permalink 找出對應內容。
     *
     * Post:
     * /posts/93227
     *
     * Comment:
     * /posts/45332/comments/336610
     */
    public function forPermalink(string $permalink): ?array
{
    $path = parse_url($permalink, PHP_URL_PATH);

    if (! $path) {
        return null;
    }

if (preg_match('#/posts/(\d+)(?:-[^/]+)?/comments/(\d+)(?:-[^/]+)?$#', $path, $matches)) {
    return [
        'contentType' => 'comment',
        'contentId' => (int) $matches[2],
    ];
}

if (preg_match('#/posts/(\d+)(?:-[^/]+)?$#', $path, $matches)) {
    return [
        'contentType' => 'post',
        'contentId' => (int) $matches[1],
    ];
}

    return null;
}

    /**
     * 根據內容取得完整 GeoIP 資料。
     */
    public function forContent(
        string $contentType,
        int $contentId,
    ): ?array {
        $ip = ContentIp::where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->value('ip_address');

        if (! $ip) {
            return null;
        }

        $countryCode = IpInfo::where('ip_address', $ip)
            ->value('country_code');

        if (! $countryCode || strlen($countryCode) !== 2) {
            return null;
        }

        $countryCode = strtoupper($countryCode);
        $locale = app()->getLocale();

        $countryName = \Locale::getDisplayRegion(
            '-' . $countryCode,
            $locale,
        ) ?: null;

        $flag = $this->twemojiUrlFromCountryCode($countryCode);

        return [
            'ip' => $ip,
            'countryCode' => $countryCode,
            'countryName' => $countryName,
            'flag' => $flag,
        ];
    }

    /**
     * 根據 permalink 取得完整 GeoIP 資料。
     */
    public function forPermalinkData(string $permalink): ?array
    {
        $content = $this->forPermalink($permalink);

        if (! $content) {
            return null;
        }

        return $this->forContent(
            $content['contentType'],
            $content['contentId'],
        );
    }

    /**
     * 取得國家代碼。
     */
    public function countryCode(
        string $contentType,
        int $contentId,
    ): ?string {
        return $this->forContent($contentType, $contentId)['countryCode'] ?? null;
    }

    /**
     * 取得國家名稱。
     */
    public function countryName(
        string $contentType,
        int $contentId,
    ): ?string {
        return $this->forContent($contentType, $contentId)['countryName'] ?? null;
    }

    /**
     * 取得 Twemoji 國旗 URL。
     */
    public function twemojiUrl(
        string $contentType,
        int $contentId,
    ): ?string {
        return $this->forContent($contentType, $contentId)['flag'] ?? null;
    }

    /**
     * 根據國家代碼產生 Twemoji URL。
     */
    protected function twemojiUrlFromCountryCode(
        string $countryCode,
    ): ?string {
        $url = config('waterhole.design.emoji_url');

        if (! $url) {
            return null;
        }

        $tseq = sprintf(
            '%x-%x',
            127397 + ord($countryCode[0]),
            127397 + ord($countryCode[1]),
        );

        return str_replace('{@tseq}', $tseq, $url);
    }
}
