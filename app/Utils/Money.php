<?php

namespace App\Utils;

use Brick\Math\RoundingMode;
use Brick\Money\Money as BrickMoney;
use NumberFormatter;
use RuntimeException;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Castable;
use Spatie\LaravelData\Support\DataProperty;

class Money implements Castable
{
    public string $currency;
    public int $amount;

    public function __construct(string $currency, int $amount)
    {
        $this->currency = $currency;
        $this->amount = $amount;
    }

    public static function ofMinor(string $currency, int $amount): static
    {
        return new static($currency, $amount);
    }

    public static function ofFormatted(string $currency, string $amount): static
    {
        if ('BRL' === $currency) {
            $amount = str_replace(',', '.', str_replace('.', '', $amount));
        } else {
            $amount = str_replace(',', '', $amount);
        }

        $brickMoney = BrickMoney::of($amount, $currency);

        return new static(
            currency: $brickMoney->getCurrency()->getCurrencyCode(),
            amount: $brickMoney->getMinorAmount()->toInt()
        );
    }

    public function getMinorAmount(): int
    {
        return $this->getBrickMoney()->getMinorAmount()->toInt();
    }

    public function getFormattedAmount(): string
    {
        if ('BRL' === $this->currency) {
            $formatter = new NumberFormatter('pt_BR', NumberFormatter::DECIMAL);
            $formatter->setSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, ',');
            $formatter->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, '.');
        } else {
            $formatter = new NumberFormatter('en_US', NumberFormatter::DECIMAL);
            $formatter->setSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, '.');
            $formatter->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, ',');
        }

        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);

        return $this->getBrickMoney()->formatWith($formatter);
    }

    public function getBrickMoney(): BrickMoney
    {
        return BrickMoney::ofMinor(
            minorAmount: $this->amount,
            currency: $this->currency,
            roundingMode: RoundingMode::HALF_UP
        );
    }

    public static function dataCastUsing(...$arguments): Cast
    {
        return new class implements Cast
        {
            public function cast(DataProperty $property, mixed $value, array $context): Money
            {
                if ($value instanceof Money) {
                    return $value;
                }

                if (is_array($value)) {
                    return new Money(
                        currency: $value['currency'],
                        amount: $value['amount']
                    );
                }

                throw new RuntimeException('Invalid money value');
            }
        };
    }
}
