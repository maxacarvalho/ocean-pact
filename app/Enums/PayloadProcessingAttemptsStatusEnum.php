<?php

namespace App\Enums;

use App\Utils\Str;
use Spatie\Enum\Laravel\Enum;

/**
 * @method static self SUCCESS()
 * @method static self FAILED()
 */
class PayloadProcessingAttemptsStatusEnum extends Enum
{
    protected static function labels(): array
    {
        return [
            'SUCCESS' => Str::formatTitle(__('payload_processing_attempt.success')),
            'FAILED' => Str::formatTitle(__('payload_processing_attempt.failed')),
        ];
    }
}
