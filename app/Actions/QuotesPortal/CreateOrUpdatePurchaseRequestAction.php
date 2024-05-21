<?php

namespace App\Actions\QuotesPortal;

use App\Enums\QuotesPortal\PurchaseRequestStatus;
use App\Http\Requests\QuotesPortal\StoreOrUpdatePurchaseRequestData;
use App\Models\QuotesPortal\PurchaseRequest;

class CreateOrUpdatePurchaseRequestAction
{
    public function handle(StoreOrUpdatePurchaseRequestData $storeOrUpdatePurchaseRequestData): PurchaseRequest
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::query()->updateOrCreate(
            [
                PurchaseRequest::QUOTE_ID => $storeOrUpdatePurchaseRequestData->quoteId,
                PurchaseRequest::PURCHASE_REQUEST_NUMBER => $storeOrUpdatePurchaseRequestData->purchaseRequestNumber,
            ],
            [
                PurchaseRequest::STATUS => $storeOrUpdatePurchaseRequestData->status,
                PurchaseRequest::FILE => $storeOrUpdatePurchaseRequestData->file,
            ]
        );

        if ($purchaseRequest->status === PurchaseRequestStatus::APPROVED) {
            $purchaseRequest->update([
                PurchaseRequest::SENT_AT => now(),
            ]);
        }

        return $purchaseRequest;
    }
}
