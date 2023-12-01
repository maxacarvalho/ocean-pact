<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\QuoteData;
use App\Data\QuotesPortal\QuoteItemData;
use App\Data\QuotesPortal\SellerData;
use App\Events\QuotePortal\QuoteCreatedEvent;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use App\Models\QuotesPortal\SupplierUser;
use Illuminate\Validation\ValidationException;

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
        /** @var Company $company */
        $company = Company::query()->findOrFail($quotePayloadData->company_id);

        $buyer = $this->findOrCreateBuyerAction->handle($quotePayloadData);

        $supplier = $this->findOrCreateSupplierAction->handle($quotePayloadData, $company);

        $hasDuplicatedQuote = Quote::query()
            ->where(Quote::QUOTE_NUMBER, '=', $quotePayloadData->quote_number)
            ->where(Quote::COMPANY_ID, '=', $company->id)
            ->where(Quote::SUPPLIER_ID, '=', $supplier->id)
            ->exists();

        if ($hasDuplicatedQuote) {
            throw ValidationException::withMessages([
                Quote::QUOTE_NUMBER => __('quote.validation_duplicated_quote_for_company', [
                    'company_code' => $company->code,
                    'company_code_branch' => $company->code_branch,
                    'quote_number' => $quotePayloadData->quote_number,
                    'supplier_name' => $supplier->name,
                ]),
            ]);
        }

        $sellers = [];
        /** @var SellerData $seller */
        foreach ($quotePayloadData->supplier->sellers as $seller) {
            $createdSeller = $this->createSellerAction->handle($seller);

            $sellers[$createdSeller->id] = [SupplierUser::CODE => $seller->supplier_user->code];
        }

        $supplier->sellers()->sync($sellers);

        $budget = $this->findOrCreateBudgetAction->handle($quotePayloadData, $company);
        $currency = $this->findOrCreateCurrencyAction->handle($quotePayloadData, $company);
        $paymentCondition = $this->findOrCreatePaymentConditionAction->handle($quotePayloadData, $company);
        $codeToProductsMapping = $this->findOrCreateProductsAction->handle($quotePayloadData, $company);
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

        QuoteCreatedEvent::dispatch($quote->id);

        return $quote;
    }
}
