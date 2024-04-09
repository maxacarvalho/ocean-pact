<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\StoreQuotePayloadData;
use App\Enums\QuotesPortal\QuoteStatusEnum;
use App\Events\QuotePortal\QuoteCreatedEvent;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;

class CreateQuotesAction
{
    public function handle(
        StoreQuotePayloadData $data,
        Company $company,
        int $paymentConditionId,
        int $buyerId,
        int $budgetId,
        int $currencyId,
        array $mappingCodesAndStoresToSuppliersIds,
        array $mappingCodesToProductsIds
    ): array {
        $commonData = [
            Quote::PROPOSAL_NUMBER => $data->proposalNumber,
            Quote::COMPANY_ID => $company->id,
            Quote::PAYMENT_CONDITION_ID => $paymentConditionId,
            Quote::BUYER_ID => $buyerId,
            Quote::BUDGET_ID => $budgetId,
            Quote::QUOTE_NUMBER => $data->quoteNumber,
            Quote::STATUS => QuoteStatusEnum::PENDING,
            Quote::COMMENTS => $data->comments,
            Quote::CURRENCY_ID => $currencyId,
        ];

        $quotesIds = [];

        foreach ($data->suppliers as $supplierData) {
            /** @var Quote $quote */
            $quote = Quote::query()->create(
                array_merge(
                    $commonData,
                    [
                        Quote::SUPPLIER_ID => $mappingCodesAndStoresToSuppliersIds[$supplierData->code.'-'.$supplierData->store],
                    ]
                )
            );

            foreach ($data->items as $itemData) {
                QuoteItem::query()->create([
                    QuoteItem::QUOTE_ID => $quote->id,
                    QuoteItem::PRODUCT_ID => $mappingCodesToProductsIds[$itemData->product->code],
                    QuoteItem::DESCRIPTION => $itemData->description,
                    QuoteItem::MEASUREMENT_UNIT => $itemData->measurementUnit,
                    QuoteItem::ITEM => $itemData->item,
                    QuoteItem::QUANTITY => $itemData->quantity,
                    QuoteItem::UNIT_PRICE => $itemData->unitPrice,
                    QuoteItem::CURRENCY => $data->currency->protheusAcronym,
                    QuoteItem::COMMENTS => $itemData->comments,
                ]);
            }

            QuoteCreatedEvent::dispatch($quote->id);

            $quotesIds[] = $quote->id;
        }

        return $quotesIds;
    }
}
