<?php

namespace App\Enums;

use App\Utils\Str;
use Filament\Support\Contracts\HasLabel;

enum PayloadProcessingAttemptsStatusEnum: string implements HasLabel
{
    use EnumHelperTrait;

    case SUCCESS = 'SUCCESS';
    case FAILED = 'FAILED';

    public function getLabel(): string
    {
        return match ($this->value) {
            'SUCCESS' => Str::formatTitle(__('payload_processing_attempt.success')),
            'FAILED' => Str::formatTitle(__('payload_processing_attempt.failed')),
        };
    }
}
