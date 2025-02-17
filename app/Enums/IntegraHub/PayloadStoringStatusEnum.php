<?php

namespace App\Enums\IntegraHub;

use App\Enums\EnumHelperTrait;
use App\Utils\Str;
use Filament\Support\Contracts\HasLabel;

enum PayloadStoringStatusEnum: string implements HasLabel
{
    use EnumHelperTrait;

    case STORED = 'STORED';
    case FAILED = 'FAILED';

    public function getLabel(): string
    {
        return match ($this->value) {
            'STORED' => Str::formatTitle(__('payload.stored')),
            'FAILED' => Str::formatTitle(__('payload.failed')),
        };
    }
}
