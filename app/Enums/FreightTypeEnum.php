<?php

namespace App\Enums;

use App\Utils\Str;
use Spatie\Enum\Laravel\Enum;

/**
 * @method static self CIF()
 * @method static self FOB()
 */
class FreightTypeEnum extends Enum
{
    protected static function labels(): array
    {
        return [
            'CIF' => Str::formatTitle(__('quote_item.CIF')),
            'FOB' => Str::formatTitle(__('quote_item.FOB')),
        ];
    }
}
