<?php

namespace App\Livewire\Synth;

use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class MoneySynth extends Synth
{
    public static $key = 'mny';

    public static function match($target): bool
    {
        return $target instanceof Money;
    }

    public function dehydrate($target): array
    {
        return [
            [
                'currency' => $target->getCurrency()->getCurrencyCode(),
                'amount' => $target->getMinorAmount()->toInt(),
            ],
            [],
        ];
    }

    public function hydrate($value): Money
    {
        return Money::ofMinor(
            minorAmount: $value['amount'],
            currency: $value['currency'],
            roundingMode: RoundingMode::UP
        );
    }
}
