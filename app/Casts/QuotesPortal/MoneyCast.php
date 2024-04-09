<?php

namespace App\Casts\QuotesPortal;

use App\Models\QuotesPortal\QuoteItem;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class MoneyCast implements Cast, CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Money
    {
        $currency = $attributes[QuoteItem::CURRENCY];

        return Money::ofMinor(
            minorAmount: $value,
            currency: $currency,
            roundingMode: RoundingMode::UP
        );
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        if ($value instanceof Money) {
            return $value->getMinorAmount()->toInt();
        }

        $currency = $attributes[QuoteItem::CURRENCY];

        $value = match ($currency) {
            'BRL' => str_replace(',', '.', str_replace('.', '', $value)), // R$ 1.000,00 -> R$ 1000.00
            default => str_replace('.', '', $value), // R$ 1000 -> R$ 1000.00
        };

        return Money::of(
            amount: $value,
            currency: $currency,
            roundingMode: RoundingMode::UP
        )->getMinorAmount()->toInt();
    }

    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        $currency = 'BRL';

        if (isset($properties[QuoteItem::CURRENCY]) && is_string($properties[QuoteItem::CURRENCY])) {
            $currency = $properties[QuoteItem::CURRENCY];
        }

        return Money::ofMinor(
            minorAmount: $value,
            currency: $currency,
            roundingMode: RoundingMode::UP
        );
    }
}
