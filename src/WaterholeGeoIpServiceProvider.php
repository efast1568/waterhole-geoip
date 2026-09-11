<?php

namespace Subarist\WaterholeGeoIp;

use Subarist\WaterholeGeoIp\Jobs\ProcessGeoIp;
use Subarist\WaterholeGeoIp\Services\GeoIpProviderInterface;
use Subarist\WaterholeGeoIp\Services\Providers\IpApiProvider;
use Subarist\WaterholeGeoIp\Services\Providers\IpInfoProvider;
use Waterhole\Extend;
use Waterhole\Models\Comment;
use Waterhole\Models\Post;

class WaterholeGeoIpServiceProvider extends Extend\ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/waterhole-geoip.php',
            'waterhole-geoip',
        );
    }

    public function boot(): void
    {
        $this->registerResources();

        Post::saved(fn (Post $post) => $this->dispatchIpJob('post', $post));
        Comment::saved(fn (Comment $comment) => $this->dispatchIpJob('comment', $comment));
    }

    protected function registerResources(): void
    {
        $this->app->singleton(
            GeoIpProviderInterface::class,
            fn () => match (config('waterhole-geoip.provider')) {
                'ipinfo' => app(IpInfoProvider::class),
                'ip-api' => app(IpApiProvider::class),
                default => app(IpApiProvider::class),
            },
        );

        $this->loadViewsFrom(
            __DIR__ . '/../resources/views',
            'waterhole-geoip',
        );

        $this->loadMigrationsFrom(
            __DIR__ . '/../database/migrations',
        );

        $this->loadTranslationsFrom(
            __DIR__ . '/../lang',
            'waterhole-geoip',
        );

        app('view')->getFinder()->prependNamespace(
            'waterhole',
            __DIR__ . '/../resources/views',
        );

        $this->extend(function (Extend\Assets\Stylesheet $stylesheet) {
            $stylesheet->add(
                __DIR__ . '/../resources/css/geoip.css',
            );
        });

        $this->extend(function (Extend\Forms\UserForm $form) {
            $form->profile->add(
                \Subarist\WaterholeGeoIp\Forms\UserShowIpCountry::class,
                'showIpCountry',
                100,
            );
        });
    }

    protected function dispatchIpJob(string $type, $model): void
    {
        $ip = request()->ip();

        if ($model->wasRecentlyCreated && $this->isValidPublicIp($ip)) {
            ProcessGeoIp::dispatch(
                $type,
                $model->id,
                $ip,
            );
        }
    }

    protected function isValidPublicIp(?string $ip): bool
    {
        return $ip && filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
        ) !== false;
    }
}