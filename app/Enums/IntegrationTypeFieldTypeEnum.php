<?php

namespace App\Enums;

use App\Utils\Str;

/**
 * @method static self date()
 * @method static self float()
 * @method static self integer()
 * @method static self boolean()
 * @method static self string()
 */
class IntegrationTypeFieldTypeEnum extends \Spatie\Enum\Laravel\Enum
{
    protected static function labels(): array
    {
        return [
            'string' => Str::formatTitle(__('integration_type_field.string')),
            'integer' => Str::formatTitle(__('integration_type_field.integer')),
            'float' => Str::formatTitle(__('integration_type_field.float')),
            'boolean' => Str::formatTitle(__('integration_type_field.boolean')),
            'date' => Str::formatTitle(__('integration_type_field.date')),
        ];
    }
}
