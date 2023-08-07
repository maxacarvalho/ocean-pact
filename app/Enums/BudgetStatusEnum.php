<?php

namespace App\Enums;

use App\Utils\Str;
use Filament\Support\Contracts\HasLabel;

enum BudgetStatusEnum: string implements HasLabel
{
    case OPEN = 'OPEN';
    case CLOSED = 'CLOSED';

    public function getLabel(): string
    {
        return match ($this->value) {
            'OPEN' => Str::formatTitle(__('budget.open')),
            'CLOSED' => Str::formatTitle(__('budget.closed')),
        };
    }
}
