<?php

namespace App\Enums\IntegraHub;

use App\Enums\EnumHelperTrait;
use App\Utils\Str;
use Filament\Support\Contracts\HasLabel;

enum IntegrationTypeSchedulingOptionsEnum: string implements HasLabel
{
    use EnumHelperTrait;

    case daily = 'daily';
    case hourly = 'hourly';
    case custom = 'custom';

    public function getLabel(): string
    {
        return match ($this->value) {
            'daily' => Str::formatTitle(__('integration_type.scheduling_settings.daily')),
            'hourly' => Str::formatTitle(__('integration_type.scheduling_settings.hourly')),
            'custom' => Str::formatTitle(__('integration_type.scheduling_settings.custom')),
        };
    }
}
