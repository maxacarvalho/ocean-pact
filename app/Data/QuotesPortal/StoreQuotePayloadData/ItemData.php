<?php

namespace App\Data\QuotesPortal\StoreQuotePayloadData;

use App\Casts\QuotesPortal\MoneyCast;
use Brick\Money\Money;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ItemData extends Data
{
    public function __construct(
        public readonly string $description,
        #[MapInputName('measurement_unit')]
        public readonly string $measurementUnit,
        public readonly string $item,
        public readonly float $quantity,
        #[MapInputName('unit_price'), WithCast(MoneyCast::class)]
        public readonly Money $unitPrice,
        public readonly float $ipi,
        public readonly float $icms,
        public readonly string|null|Optional $comments,
        public readonly ProductData $product,
    ) {
        //
    }
}
