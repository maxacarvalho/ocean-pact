<?php

namespace App\Enums;

use App\Utils\Str;
use Spatie\Enum\Laravel\Enum;

/**
 * @method static self OPEN()
 * @method static self CLOSED()
 */
class BudgetStatusEnum extends Enum
{
    protected static function labels(): array
    {
        return [
            'OPEN' => Str::formatTitle(__('budget.open')),
            'CLOSED' => Str::formatTitle(__('budget.closed')),
        ];
    }
}
