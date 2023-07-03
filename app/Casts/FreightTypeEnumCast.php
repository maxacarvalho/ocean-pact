<?php

namespace App\Casts;

use App\Enums\FreightTypeEnum;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\DataProperty;

class FreightTypeEnumCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $context): FreightTypeEnum
    {
        return FreightTypeEnum::from($value);
    }
}
