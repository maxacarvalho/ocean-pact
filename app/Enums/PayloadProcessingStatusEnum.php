<?php

namespace App\Enums;

use App\Utils\Str;
use Filament\Support\Contracts\HasLabel;

enum PayloadProcessingStatusEnum: string implements HasLabel
{
    use EnumHelperTrait;

    case READY = 'READY';
    case PROCESSING = 'PROCESSING';
    case COLLECTED = 'COLLECTED';
    case DONE = 'DONE';
    case FAILED = 'FAILED';

    public function getLabel(): string
    {
        return match ($this->value) {
            'READY' => Str::formatTitle(__('payload.ready')),
            'PROCESSING' => Str::formatTitle(__('payload.processing')),
            'COLLECTED' => Str::formatTitle(__('payload.collected')),
            'DONE' => Str::formatTitle(__('payload.done')),
            'FAILED' => Str::formatTitle(__('payload.failed')),
        };
    }
}
