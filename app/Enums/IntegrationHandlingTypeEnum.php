<?php

namespace App\Enums;

use App\Utils\Str;
use Filament\Support\Contracts\HasLabel;

enum IntegrationHandlingTypeEnum: string implements HasLabel
{
    case SEND = 'SEND';
    case STORE = 'STORE';
    case STORE_AND_SEND = 'STORE_AND_SEND';
    case STORE_AND_PROCESS = 'STORE_AND_PROCESS';

    public function getLabel(): string
    {
        return match ($this->value) {
            'SEND' => Str::formatTitle(__('integration_type.send')),
            'STORE' => Str::formatTitle(__('integration_type.store')),
            'STORE_AND_SEND' => Str::formatTitle(__('integration_type.store_and_send')),
            'STORE_AND_PROCESS' => Str::formatTitle(__('integration_type.store_and_process')),
        };
    }
}
