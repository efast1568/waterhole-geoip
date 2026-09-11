@blaze

@props([
    'user',
    'date' => null,
    'permalink' => null,
    'editDate' => null,
    'primaryTarget' => false,
])

@php
    $geoip = null;

    if ($user?->show_ip_country) {
        $geoipService = app(\Subarist\WaterholeGeoIp\Services\GeoIpFlagService::class);

        if ($permalink) {
            $geoip = $geoipService->forPermalinkData($permalink);
        }

        if (!$geoip && ($comment = request()->route('comment'))) {
            $geoip = $geoipService->forContent('comment', $comment->id);
        }
    }
@endphp

<div
    {{
        $attributes->class('attribution')->merge([
            'data-group' => $user?->groups
                ->where('is_public', true)
                ->pluck('id')
                ->join(' '),
        ])
    }}
>
    <span class="attribution__user">
        <x-waterhole::user-link :user="$user" class="attribution__link">
            <x-waterhole::avatar :user="$user" />

            @if ($user?->isOnline())
                <span class="dot color-success">
                    <ui-tooltip>
                        {{ __('waterhole::user.online-label') }}
                    </ui-tooltip>
                </span>
            @endif

            <span class="attribution__name">
                {{ Waterhole\username($user) }}
            </span>
        </x-waterhole::user-link>

        @if (!empty($geoip['flag']))
            <span class="geoip-flag">
                <img
                    src="{{ $geoip['flag'] }}"
                    alt=""
                    height="18"
                >

                @if (!empty($geoip['countryName']))
                    <ui-tooltip>
                        {{ $geoip['countryName'] }}
                    </ui-tooltip>
                @endif
            </span>
        @endif

        <x-waterhole::user-groups :user="$user" />
    </span>

    <span class="attribution__info">
        @if ($user?->headline)
            <span>{{ $user->headline }}</span>
        @endif

        @if ($displayDate = $editDate ?: $date)
            <span>
                {{-- format-ignore-start --}}
                @if ($permalink)
                    <a
                        href="{{ $permalink }}"
                        class="color-inherit with-icon"
                        target="_top"
                        @if ($primaryTarget)
                            data-shortcut-selection-primary
                            data-shortcut-trigger="selection.open"
                        @endif
                    >
                @else
                    <span class="with-icon">
                @endif

                    @if ($editDate)
                        @icon('tabler-pencil', ['class' => 'icon--narrow text-xxs'])
                    @endif

                    <x-waterhole::relative-time
                        :datetime="$displayDate"
                        title=""
                    />

                    <ui-tooltip
                        placement="bottom"
                        tooltip-class="tooltip tooltip--block"
                    >
                        @if ($date)
                            <div>
                                <small>
                                    {{ __('waterhole::forum.attribution-timestamp-created-label') }}
                                </small>

                                {{ $date->locale(app()->getLocale())->isoFormat('llll') }}
                            </div>
                        @endif

                        @if ($editDate)
                            <div>
                                <small>
                                    {{ __('waterhole::forum.attribution-timestamp-edited-label') }}
                                </small>

                                {{ $editDate->locale(app()->getLocale())->isoFormat('llll') }}
                            </div>
                        @endif
                    </ui-tooltip>

                @if ($permalink)
                    </a>
                @else
                    </span>
                @endif
                {{-- format-ignore-end --}}
            </span>
        @endif
    </span>
</div>