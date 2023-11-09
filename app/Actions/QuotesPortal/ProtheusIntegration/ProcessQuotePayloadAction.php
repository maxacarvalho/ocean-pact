<?php

namespace App\Actions\QuotesPortal\ProtheusIntegration;

use App\Data\QuotesPortal\QuoteData;
use App\Data\QuotesPortal\QuoteItemData;
use App\Data\QuotesPortal\SellerData;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use Illuminate\Database\Eloquent\ModelNotFoundException;

readonly class ProcessQuotePayloadAction
{
    public function __construct(
        private FindOrCreateBuyerAction $findOrCreateBuyerAction,
        private FindOrCreateSupplierAction $findOrCreateSupplierAction,
        private FindOrCreateBudgetAction $findOrCreateBudgetAction,
        private FindOrCreateCurrencyAction $findOrCreateCurrencyAction,
        private FindOrCreatePaymentConditionAction $findOrCreatePaymentConditionAction,
        private FindOrCreateProductsAction $findOrCreateProductsAction,
        private CreateQuoteAction $createQuoteAction,
        private CreateSellerAction $createSellerAction
    ) {
        //
    }

    public function handle(QuoteData $quotePayloadData): Quote
    {
        $company = $this->getCompany($quotePayloadData);

        $buyer = $this->findOrCreateBuyerAction->handle($quotePayloadData, $company);

        $supplier = $this->findOrCreateSupplierAction->handle($quotePayloadData, $company);

        /** @var SellerData $seller */
        foreach ($quotePayloadData->supplier->sellers as $seller) {
            $this->createSellerAction->handle($seller, $supplier);
        }

        $budget = $this->findOrCreateBudgetAction->handle($quotePayloadData);
        $currency = $this->findOrCreateCurrencyAction->handle($quotePayloadData);
        $paymentCondition = $this->findOrCreatePaymentConditionAction->handle($quotePayloadData);
        $codeToProductsMapping = $this->findOrCreateProductsAction->handle($quotePayloadData);
        $quote = $this->createQuoteAction->handle($budget, $currency, $supplier, $paymentCondition, $buyer, $quotePayloadData);

        /** @var QuoteItemData $item */
        foreach ($quotePayloadData->items as $item) {
            $quote->items()->create([
                QuoteItem::PRODUCT_ID => $codeToProductsMapping[$item->product->code],
                QuoteItem::DESCRIPTION => $item->description,
                QuoteItem::MEASUREMENT_UNIT => $item->measurement_unit,
                QuoteItem::ITEM => $item->item,
                QuoteItem::QUANTITY => $item->quantity,
                QuoteItem::CURRENCY => $currency->iso_code,
                QuoteItem::UNIT_PRICE => $item->unit_price,
                QuoteItem::COMMENTS => $item->comments,
            ]);
        }

        $quote->markAsPending();
        $quote->refresh();

        return $quote;
    }

    /** @throws ModelNotFoundException */
    private function getCompany(QuoteData $data): Company
    {
        /** @var Company $company */
        $company = Company::query()
            ->where(Company::CODE, '=', $data->company_code)
            ->where(Company::CODE_BRANCH, '=', $data->company_code_branch)
            ->firstOrFail();

        return $company;
    }
}
