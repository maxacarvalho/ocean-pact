<?php

namespace App\Enums;

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
            'SUCCESS' => __('payload_processing_attempt.SUCCESS'),
            'FAILED' => __('payload_processing_attempt.FAILED'),
        ];
    }
}
