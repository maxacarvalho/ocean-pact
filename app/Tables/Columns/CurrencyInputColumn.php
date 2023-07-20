<?php

namespace App\Tables\Columns;

use App\Utils\Money;
use Brick\Math\Exception\NumberFormatException;
use Filament\Tables\Columns\TextInputColumn;

class CurrencyInputColumn extends TextInputColumn
{
    protected string $view = 'tables.columns.currency-input-column';

    public function getState()
    {
        $state = $this->getStateFromRecord();

        if (null === $state) {
            $state = 0;
        }

        return Money::fromMinor($state)->toDecimal();
    }

    public function updateState(mixed $state): mixed
    {
        try {
            $toDb = Money::fromMonetary($state)->toMinor();
        } catch (NumberFormatException $exception) {
            $toDb = Money::parse($state)->toMinor();
        }

        parent::updateState($toDb);

        return $state;
    }
}
