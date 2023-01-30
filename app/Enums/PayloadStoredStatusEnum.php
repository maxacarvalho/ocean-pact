<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self STORED()
 * @method static self FAILED()
 */
class PayloadStoredStatusEnum extends Enum
{
    protected static function labels(): array
    {
        return [
            'STORED' => __('payload.STORED'),
            'FAILED' => __('payload.FAILED'),
        ];
    }
}
