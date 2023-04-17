<?php

namespace App\Enums;

use App\Utils\Str;
use Spatie\Enum\Laravel\Enum;

/**
 * @method static self READY()
 * @method static self PROCESSING()
 * @method static self COLLECTED()
 * @method static self DONE()
 * @method static self FAILED()
 */
class PayloadProcessingStatusEnum extends Enum
{
    protected static function labels(): array
    {
        return [
            'READY' => Str::formatTitle(__('payload.ready')),
            'PROCESSING' => Str::formatTitle(__('payload.processing')),
            'COLLECTED' => Str::formatTitle(__('payload.collected')),
            'DONE' => Str::formatTitle(__('payload.done')),
            'FAILED' => Str::formatTitle(__('payload.failed')),
        ];
    }
}
