<?php

namespace App\Http\Requests\QuotesPortal;

use App\Enums\QuotesPortal\PurchaseRequestStatus;
use App\Http\Requests\QuotesPortal\StoreOrUpdatePurchaseRequest\StoreOrUpdatePurchaseRequestItem;
use App\Models\QuotesPortal\PurchaseRequest;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class StoreOrUpdatePurchaseRequest extends Data
{
    public function __construct(
        #[Required]
        public string $company,
        #[Required]
        public string $branch,
        #[Required]
        public string $quoteNumber,
        #[Required]
        public string $purchaseRequestNumber,
        #[Required]
        public PurchaseRequestStatus $status,
        #[RequiredIf(PurchaseRequest::STATUS, PurchaseRequestStatus::APPROVED)]
        public ?string $file,
        #[Required]
        /** @var Collection<int, StoreOrUpdatePurchaseRequestItem> */
        public Collection $items
    ) {
        //
    }
}
