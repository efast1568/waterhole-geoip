<?php

namespace Subarist\WaterholeGeoIp\Forms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Waterhole\Forms\Field;
use Waterhole\Models\User;

class UserShowIpCountry extends Field
{
    public function __construct(
        public ?User $model,
    ) {}

public function render(): string
{
    return <<<'blade'
        <div role="group" class="field">
            <div class="field__label">
                {{ __('waterhole-geoip::user.ip-address-title') }}
            </div>

            <div class="grow stack gap-xs">
                <input type="hidden" name="show_ip_country" value="0">

                <label for="show_ip_country" class="choice">
                    <input
                        id="show_ip_country"
                        type="checkbox"
                        name="show_ip_country"
                        value="1"
                        @checked($model?->show_ip_country)
                    >

                    {{ __('waterhole-geoip::user.show-ip-country-label') }}
                </label>

                @if (config('waterhole-geoip.provider') === 'ipinfo')
                    <div class="field__description">
                        {!! __('waterhole-geoip::user.show-ip-country-description') !!}
                    </div>
                @endif
            </div>
        </div>
        blade;
}

    public function validating(Validator $validator): void
    {
        $validator->addRules([
            'show_ip_country' => ['boolean'],
        ]);
    }

    public function saving(FormRequest $request): void
    {
        $this->model->show_ip_country = $request->validated('show_ip_country');
    }
}