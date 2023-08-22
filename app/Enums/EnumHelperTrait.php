<?php

namespace App\Enums;

use BackedEnum;

trait EnumHelperTrait
{
    public static function names(): array
    {
        return array_column(static::cases(), 'name');
    }

    public static function values(): array
    {
        $cases = static::cases();

        return isset($cases[0]) && $cases[0] instanceof BackedEnum
            ? array_column($cases, 'value')
            : array_column($cases, 'name');
    }

    public function equals(BackedEnum ...$others): bool
    {
        foreach ($others as $other) {
            if ($this->value === $other->value) {
                return true;
            }
        }

        return false;
    }
}
