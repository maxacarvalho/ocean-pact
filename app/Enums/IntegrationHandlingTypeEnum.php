<?php

namespace App\Enums;

use App\Utils\Str;
use Spatie\Enum\Laravel\Enum;

/**
 * @method static self SEND()
 * @method static self STORE()
 * @method static self STORE_AND_SEND()
 * @method static self STORE_AND_PROCESS()
 */
final class IntegrationHandlingTypeEnum extends Enum
{
    protected static function labels(): array
    {
        return [
            'SEND' => Str::formatTitle(__('integration_type.send')),
            'STORE' => Str::formatTitle(__('integration_type.store')),
            'STORE_AND_SEND' => Str::formatTitle(__('integration_type.store_and_send')),
            'STORE_AND_PROCESS' => Str::formatTitle(__('integration_type.store_and_process')),
        ];
    }
}
