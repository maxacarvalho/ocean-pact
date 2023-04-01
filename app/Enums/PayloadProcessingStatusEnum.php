<?php

namespace App\Enums;

use App\Utils\Str;
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
            'READY' => Str::formatTitle(__('payload.ready')),
            'COLLECTED' => Str::formatTitle(__('payload.collected')),
            'SENT_TO_CLIENT' => Str::formatTitle(__('payload.send_to_client')),
            'VALIDATION_ERROR' => Str::formatTitle(__('payload.validation_error')),
            'FAILED' => Str::formatTitle(__('payload.failed')),
        ];
    }
}
