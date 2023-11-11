<?php

namespace App\Enums\IntegraHub;

use App\Enums\EnumHelperTrait;
use App\Utils\Str;
use Filament\Support\Contracts\HasLabel;

enum IntegrationTypeFieldTypeEnum: string implements HasLabel
{
    use EnumHelperTrait;

    case date = 'date';
    case float = 'float';
    case integer = 'integer';
    case boolean = 'boolean';
    case string = 'string';
    case array = 'array';

    public function getLabel(): string
    {
        return match ($this->value) {
            'string' => Str::formatTitle(__('integration_type_field.string')),
            'integer' => Str::formatTitle(__('integration_type_field.integer')),
            'float' => Str::formatTitle(__('integration_type_field.float')),
            'boolean' => Str::formatTitle(__('integration_type_field.boolean')),
            'date' => Str::formatTitle(__('integration_type_field.date')),
            'array' => Str::formatTitle(__('integration_type_field.array')),
        };
    }
}
