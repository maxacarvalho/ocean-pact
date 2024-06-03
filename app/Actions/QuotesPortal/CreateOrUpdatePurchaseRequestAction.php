<?php

namespace App\Actions\QuotesPortal;

use App\Http\Requests\QuotesPortal\StoreOrUpdatePurchaseRequest;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\PurchaseRequest;
use App\Models\QuotesPortal\PurchaseRequestItem;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use App\Models\QuotesPortal\Supplier;

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
        $supplier = $this->getSupplier($storeOrUpdatePurchaseRequestData);

        /** @var Quote $quote */
        $quote = Quote::query()
            ->where(Quote::COMPANY_ID, $company->id)
            ->where(Quote::QUOTE_NUMBER, $storeOrUpdatePurchaseRequestData->quoteNumber)
            ->where(Quote::SUPPLIER_ID, $supplier->id)
            ->sole();

        return $quote;
    }

    private function getSupplier(StoreOrUpdatePurchaseRequest $storeOrUpdatePurchaseRequestData): Supplier
    {
        /** @var Supplier $supplier */
        $supplier = Supplier::query()
            ->where(Supplier::COMPANY_CODE, $storeOrUpdatePurchaseRequestData->company)
            ->where(Supplier::COMPANY_CODE_BRANCH, $storeOrUpdatePurchaseRequestData->branch)
            ->where(Supplier::CODE, $storeOrUpdatePurchaseRequestData->supplierCode)
            ->where(Supplier::STORE, $storeOrUpdatePurchaseRequestData->supplierStore)
            ->sole();

        return $supplier;
    }

    private function getCompany(StoreOrUpdatePurchaseRequest $storeOrUpdatePurchaseRequestData): Company
    {
        /** @var Company $company */
        $company = Company::query()
            ->where(Company::CODE, $storeOrUpdatePurchaseRequestData->company)
            ->where(Company::CODE_BRANCH, $storeOrUpdatePurchaseRequestData->branch)
            ->sole();

        return $company;
    }
}
