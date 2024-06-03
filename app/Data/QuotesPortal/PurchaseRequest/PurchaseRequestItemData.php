<?php

namespace App\Data\QuotesPortal\PurchaseRequest;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class PurchaseRequestItemData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $purchaseRequestId,
        public readonly int $quoteItemId,
        public readonly string $item,
        public readonly Carbon $createdAt,
        public readonly Carbon $updatedAt,
    ) {
        //
    }
}
