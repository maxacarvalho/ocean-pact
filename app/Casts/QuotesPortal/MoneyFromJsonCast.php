<?php

namespace App\Casts\QuotesPortal;

use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Model;

class MoneyFromJsonCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): Money
    {
        $data = Json::decode($value);

        return Money::ofMinor(
            minorAmount: $data['amount'],
            currency: $data['currency'],
            roundingMode: RoundingMode::UP
        );
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if ($value instanceof Money) {
            return Json::encode([
                'currency' => $value->getCurrency()->getCurrencyCode(),
                'amount' => $value->getMinorAmount()->toInt(),
            ]);
        }

        return Json::encode([
            'currency' => $value['currency'],
            'amount' => $value['amount'],
        ]);
    }
}
