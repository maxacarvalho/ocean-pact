<?php

namespace App\Data\QuotesPortal;

use App\Enums\QuotesPortal\PurchaseRequestStatus;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class PurchaseRequestData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $quoteId,
        public readonly string $purchaseRequestNumber,
        public readonly Carbon|null $sentAt,
        public readonly Carbon|null $viewedAt,
        public readonly PurchaseRequestStatus $status,
        public readonly string|null $file,
        public readonly Carbon|null $createdAt,
        public readonly Carbon|null $updatedAt,
    ) {
        //
    }
}
