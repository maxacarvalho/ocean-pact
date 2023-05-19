<?php

namespace App\Utils;

use Brick\Math\BigNumber;
use Brick\Money\Money as BrickMoney;
use NumberFormatter;

class Money
{
    public function __construct(
        private readonly BrickMoney $brickMoney
    ) {
    }

    public static function make(BrickMoney $brickMoney): self
    {
        return new self($brickMoney);
    }

    public static function fromMonetary(
        BigNumber|int|float|string $minorAmount,
    ): self {
        $brickMoney = BrickMoney::of(
            $minorAmount,
            config('app.currency')
        );

        return self::make($brickMoney);
    }

    public static function fromMinor(
        BigNumber|int|float|string $minorAmount,
    ): self {
        $brickMoney = BrickMoney::ofMinor(
            $minorAmount,
            config('app.currency')
        );

        return self::make($brickMoney);
    }

    public static function parse(string $value): self
    {
        $value = str_replace('.', '', $value);

        if (str_contains($value, ',')) {
            $value = str_replace(',', '.', $value);
        } else {
            $value = $value.'.00';
        }

        $money = BrickMoney::of($value, config('app.currency'));

        return self::make($money);
    }

    public function toMinor(): int
    {
        return $this->brickMoney->getMinorAmount()->toInt();
    }

    public function toCurrency(): string
    {
        return $this->brickMoney->formatTo(config('app.locale'));
    }

    public function toDecimal(): string
    {
        $formatter = new NumberFormatter('pt_BR', NumberFormatter::DECIMAL);
        $formatter->setSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, ',');
        $formatter->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, '.');
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);

        return $this->brickMoney->formatWith($formatter);
    }
}
