<?php

namespace App\Http\Requests\QuotesPortal\StoreOrUpdatePurchaseRequest;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class StoreOrUpdatePurchaseRequestItem extends Data
{
    public function __construct(
        #[Required]
        public string $purchaseRequestItem,
        #[Required]
        public string $quoteItem
    ) {
        //
    }
}
