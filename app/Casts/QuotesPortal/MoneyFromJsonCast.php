<?php

namespace App\Casts\QuotesPortal;

use App\Utils\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Model;

class MoneyFromJsonCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): Money
    {
        $data = Json::decode($value);

        return Money::ofMinor(
            currency: $data['currency'],
            amount: $data['amount'],
        );
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if ($value instanceof Money) {
            return Json::encode([
                'currency' => $value->currency,
                'amount' => $value->getMinorAmount(),
            ]);
        }

        return Json::encode([
            'currency' => $value['currency'],
            'amount' => $value['amount'],
        ]);
    }
}
