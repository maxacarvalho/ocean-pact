<?php

namespace App\Concerns;

use NumberFormatter;

trait HasMoneyAttribute
{
    public function getFormattedUnitPrice(string $attribute): string
    {
        $currency = $this->currency;

        if ('BRL' === $currency) {
            $formatter = new NumberFormatter('pt_BR', NumberFormatter::DECIMAL);
            $formatter->setSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, ',');
            $formatter->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, '.');
        } else {
            $formatter = new NumberFormatter('en_US', NumberFormatter::DECIMAL);
            $formatter->setSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, '.');
            $formatter->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, ',');
        }

        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);

        return $this->unit_price->formatWith($formatter);
    }
}
