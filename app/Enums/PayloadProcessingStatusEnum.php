<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self READY()
 * @method static self COLLECTED()
 * @method static self SENT_TO_CLIENT()
 * @method static self VALIDATION_ERROR()
 * @method static self FAILED()
 */
class PayloadProcessingStatusEnum extends Enum
{
    protected static function labels(): array
    {
        return [
            'READY' => __('payload.READY'),
            'COLLECTED' => __('payload.COLLECTED'),
            'SENT_TO_CLIENT' => __('payload.SEND_TO_CLIENT'),
            'VALIDATION_ERROR' => __('payload.VALIDATION_ERROR'),
            'FAILED' => __('payload.FAILED'),
        ];
    }
}
