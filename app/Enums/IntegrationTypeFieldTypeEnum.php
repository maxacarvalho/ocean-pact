<?php

namespace App\Enums;

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
            'string' => __('integration_type_field.string'),
            'integer' => __('integration_type_field.integer'),
            'float' => __('integration_type_field.float'),
            'boolean' => __('integration_type_field.boolean'),
            'date' => __('integration_type_field.date'),
        ];
    }
}
