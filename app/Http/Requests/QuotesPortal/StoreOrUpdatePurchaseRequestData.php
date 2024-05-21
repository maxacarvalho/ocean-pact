<?php

namespace App\Http\Requests\QuotesPortal;

use App\Enums\QuotesPortal\PurchaseRequestStatus;
use App\Models\QuotesPortal\PurchaseRequest;
use App\Models\QuotesPortal\Quote;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class StoreOrUpdatePurchaseRequestData extends Data
{
    public function __construct(
        #[Required, Exists(table: Quote::TABLE_NAME, column: Quote::ID)]
        public int $quoteId,
        public string $purchaseRequestNumber,
        public PurchaseRequestStatus $status,
        #[RequiredIf(PurchaseRequest::STATUS, PurchaseRequestStatus::APPROVED)]
        public ?string $file
    ) {
        //
    }
}
