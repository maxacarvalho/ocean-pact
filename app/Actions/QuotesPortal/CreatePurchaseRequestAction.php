<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\PurchaseRequestRequestData;
use App\Models\QuotesPortal\PurchaseRequest;

class CreatePurchaseRequestAction
{
    public function handle(PurchaseRequestRequestData $purchaseRequestRequestData): PurchaseRequest
    {
        $attributes = $purchaseRequestRequestData
            ->except(PurchaseRequest::CREATED_AT, PurchaseRequest::UPDATED_AT)
            ->toArray();

        $attributes = [
            ...$attributes,
            PurchaseRequest::SENT_AT => now(),
        ];

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::query()->create(
            $attributes
        );

        return $purchaseRequest;
    }
}
