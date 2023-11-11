<?php

namespace App\Enums\IntegraHub;

use App\Enums\EnumHelperTrait;
use App\Utils\Str;
use Filament\Support\Contracts\HasLabel;

enum IntegrationHandlingTypeEnum: string implements HasLabel
{
    use EnumHelperTrait;

    case STORE = 'STORE';
    case STORE_AND_SEND = 'STORE_AND_SEND';
    case STORE_AND_PROCESS = 'STORE_AND_PROCESS';
    case FETCH = 'FETCH';

    public function getLabel(): string
    {
        return match ($this->value) {
            'STORE' => Str::formatTitle(__('integration_type.store')),
            'STORE_AND_SEND' => Str::formatTitle(__('integration_type.store_and_send')),
            'STORE_AND_PROCESS' => Str::formatTitle(__('integration_type.store_and_process')),
            'FETCH' => Str::formatTitle(__('integration_type.fetch')),
        };
    }
}
