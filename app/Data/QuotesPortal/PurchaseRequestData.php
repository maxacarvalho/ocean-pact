<?php

namespace App\Data\QuotesPortal;

use App\Data\QuotesPortal\PurchaseRequest\PurchaseRequestItemData;
use App\Enums\QuotesPortal\PurchaseRequestStatus;
use App\Models\QuotesPortal\PurchaseRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
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
        public readonly Carbon $createdAt,
        public readonly Carbon $updatedAt,
        /** @var Collection<int, PurchaseRequestItemData> */
        public readonly Collection|Lazy $items,
    ) {
        //
    }

    public static function fromModel(PurchaseRequest $purchaseRequest): PurchaseRequestData
    {
        return new self(
            id: $purchaseRequest->id,
            quoteId: $purchaseRequest->quote_id,
            purchaseRequestNumber: $purchaseRequest->purchase_request_number,
            sentAt: $purchaseRequest->sent_at,
            viewedAt: $purchaseRequest->viewed_at,
            status: $purchaseRequest->status,
            file: $purchaseRequest->file,
            createdAt: $purchaseRequest->created_at,
            updatedAt: $purchaseRequest->updated_at,
            items: Lazy::whenLoaded('items', $purchaseRequest, fn () => PurchaseRequestItemData::collect($purchaseRequest->items)),
        );
    }
}
