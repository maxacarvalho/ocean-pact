<?php

namespace App\Data\QuotesPortal\StoreQuotePayloadData;

use App\Utils\Money;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCastable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ProductData extends Data
{
    public function __construct(
        public readonly string $code,
        public readonly string $description,
        #[MapInputName('measurement_unit')]
        public readonly string $measurementUnit,
        #[MapInputName('last_price'), WithCastable(Money::class)]
        public readonly Money|Optional $lastPrice,
        #[MapInputName('smallest_price'), WithCastable(Money::class)]
        public readonly Money|Optional $smallestPrice,
        #[MapInputName('smallest_eta')]
        public readonly int $smallestEta,
    ) {
        //
    }
}
