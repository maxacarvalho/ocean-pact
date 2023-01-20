<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self SEND()
 * @method static self STORE()
 * @method static self STORE_AND_SEND()
 */
final class IntegrationHandlingTypeEnum extends Enum
{
    protected static function labels(): array
    {
        return [
            'SEND' => __('integration_type.SEND'),
            'STORE' => __('integration_type.STORE'),
            'STORE_AND_SEND' => __('integration_type.STORE_AND_SEND'),
        ];
    }
}
