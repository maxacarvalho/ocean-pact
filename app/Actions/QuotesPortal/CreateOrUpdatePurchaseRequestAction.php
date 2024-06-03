<?php

namespace App\Actions\QuotesPortal;

use App\Http\Requests\QuotesPortal\StoreOrUpdatePurchaseRequest;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\PurchaseRequest;
use App\Models\QuotesPortal\PurchaseRequestItem;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;

class CreateOrUpdatePurchaseRequestAction
{
    public function handle(StoreOrUpdatePurchaseRequest $storeOrUpdatePurchaseRequest): PurchaseRequest
    {
        $quote = $this->getQuote($storeOrUpdatePurchaseRequest);

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::query()->updateOrCreate(
            [
                PurchaseRequest::QUOTE_ID => $quote->id,
                PurchaseRequest::PURCHASE_REQUEST_NUMBER => $storeOrUpdatePurchaseRequest->purchaseRequestNumber,
            ],
            [
                PurchaseRequest::STATUS => $storeOrUpdatePurchaseRequest->status,
                PurchaseRequest::FILE => $storeOrUpdatePurchaseRequest->file,
            ]
        );

        foreach ($storeOrUpdatePurchaseRequest->items as $item) {
            /** @var QuoteItem $quoteItem */
            $quoteItem = QuoteItem::query()
                ->where(QuoteItem::QUOTE_ID, $quote->id)
                ->where(QuoteItem::ITEM, $item->quoteItem)
                ->firstOrFail();

            $purchaseRequest->items()->updateOrCreate([
                PurchaseRequestItem::QUOTE_ITEM_ID => $quoteItem->id,
                PurchaseRequestItem::ITEM => $item->purchaseRequestItem,
            ]);
        }

        $quote->items()->whereIn(QuoteItem::ITEM, $storeOrUpdatePurchaseRequest->items->pluck('quoteItem'))->update([
            QuoteItem::PURCHASE_REQUEST_ID => $purchaseRequest->id,
        ]);

        return $purchaseRequest;
    }

    private function getQuote(StoreOrUpdatePurchaseRequest $storeOrUpdatePurchaseRequestData): Quote
    {
        $company = $this->getCompany($storeOrUpdatePurchaseRequestData);

        /** @var Quote $quote */
        $quote = Quote::query()
            ->where(Quote::COMPANY_ID, $company->id)
            ->where(Quote::QUOTE_NUMBER, $storeOrUpdatePurchaseRequestData->quoteNumber)
            ->firstOrFail();

        return $quote;
    }

    private function getCompany(StoreOrUpdatePurchaseRequest $storeOrUpdatePurchaseRequestData): Company
    {
        /** @var Company $company */
        $company = Company::query()
            ->where(Company::CODE, $storeOrUpdatePurchaseRequestData->company)
            ->where(Company::CODE_BRANCH, $storeOrUpdatePurchaseRequestData->branch)
            ->firstOrFail();

        return $company;
    }
}
