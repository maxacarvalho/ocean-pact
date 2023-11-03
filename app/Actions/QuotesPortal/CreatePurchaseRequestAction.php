<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\PurchaseRequestRequestData;
use App\Models\QuotesPortal\PurchaseRequest;

class CreatePurchaseRequestAction
{
    public function handle(PurchaseRequestRequestData $purchaseRequestRequestData): PurchaseRequest
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::query()->create(
            $purchaseRequestRequestData->except(PurchaseRequest::CREATED_AT, PurchaseRequest::UPDATED_AT)->toArray()
        );

        return $purchaseRequest;
    }
}
