<?php

namespace App\Enums;

use App\Utils\Str;
use Spatie\Enum\Laravel\Enum;

/**
 * @method static self STORED()
 * @method static self FAILED()
 */
class PayloadStoringStatusEnum extends Enum
{
    protected static function labels(): array
    {
        return [
            'STORED' => Str::formatTitle(__('payload.stored')),
            'FAILED' => Str::formatTitle(__('payload.failed')),
        ];
    }
}
