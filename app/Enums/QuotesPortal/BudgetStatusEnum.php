<?php

namespace App\Enums\QuotesPortal;

use App\Enums\EnumHelperTrait;
use App\Utils\Str;
use Filament\Support\Contracts\HasLabel;

enum BudgetStatusEnum: string implements HasLabel
{
    use EnumHelperTrait;

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
