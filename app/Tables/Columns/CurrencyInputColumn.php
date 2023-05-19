<?php

namespace App\Tables\Columns;

use App\Utils\Money;
use Filament\Tables\Columns\TextInputColumn;

class CurrencyInputColumn extends TextInputColumn
{
    protected string $view = 'tables.columns.currency-input-column';

    public function updateState(mixed $state): mixed
    {
        $toDb = Money::parse($state)->toMinor();

        parent::updateState($toDb);

        return $state;
    }
}
