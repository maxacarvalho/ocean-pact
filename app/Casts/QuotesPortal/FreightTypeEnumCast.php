<?php

namespace App\Casts\QuotesPortal;

use App\Enums\QuotesPortal\FreightTypeEnum;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class FreightTypeEnumCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): FreightTypeEnum
    {
        return FreightTypeEnum::from($value);
    }
}
