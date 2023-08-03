<?php

namespace App\Tables\Columns;

use App\Utils\Money;
use Brick\Math\Exception\NumberFormatException;
use Filament\Tables\Columns\TextInputColumn;

class CurrencyInputColumn extends TextInputColumn
{
    protected string $view = 'tables.columns.currency-input-column';
    protected bool $isDecimal = false;

    public function getState()
    {
        $state = $this->getStateFromRecord();

        if (null === $state) {
            $state = 0;
        }

        if ($this->isDecimal) {
            return number_format($state, 2, ',', '.');
        }

        return Money::fromMinor($state)->toDecimal();
    }

    public function updateState(mixed $state): mixed
    {
        try {
            $toDb = Money::fromMonetary($state);
        } catch (NumberFormatException $exception) {
            $toDb = Money::parse($state);
        }

        if ($this->isDecimal) {
            $toDb = (string) $toDb->getBrickMoney()->getAmount();
        } else {
            $toDb = $toDb->toMinor();
        }

        parent::updateState($toDb);

        return $state;
    }

    public function isDecimal(): static
    {
        $this->isDecimal = true;

        return $this;
    }
}
