<?php

namespace App\Actions\QuotesPortal;

use App\Enums\QuotesPortal\QuoteAnalysisActionEnum;
use App\Enums\QuotesPortal\QuoteStatusEnum;
use App\Events\QuotePortal\QuoteCreatedEvent;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteAnalysisAction;
use App\Models\QuotesPortal\QuoteItem;

class AddNewSupplierToQuoteAction
{
    public function handle(int $companyId, string $quoteNumber, int $newSupplierId): void
    {
        /** @var Quote $quoteSample */
        $quoteSample = Quote::query()
            ->where(Quote::COMPANY_ID, $companyId)
            ->where(Quote::QUOTE_NUMBER, $quoteNumber)
            ->firstOrFail();

        $newQuote = $quoteSample->replicate();

        $newQuote->forceFill([
            Quote::PROPOSAL_NUMBER => 1,
            Quote::SUPPLIER_ID => $newSupplierId,
            Quote::STATUS => QuoteStatusEnum::PENDING,
            Quote::COMMENTS => null,
            Quote::EXPENSES => 0,
            Quote::FREIGHT_COST => 0,
            Quote::FREIGHT_TYPE => null,
            Quote::REPLACED_BY => null,
            Quote::CREATED_AT => now(),
            Quote::UPDATED_AT => now(),
        ])->save();

        $quoteSample->load(Quote::RELATION_ITEMS);

        /** @var QuoteItem $item */
        foreach ($quoteSample->items as $item) {
            $itemData = $item->only([
                QuoteItem::PRODUCT_ID,
                QuoteItem::DESCRIPTION,
                QuoteItem::MEASUREMENT_UNIT,
                QuoteItem::ITEM,
                QuoteItem::QUANTITY,
                QuoteItem::CURRENCY,
                QuoteItem::UNIT_PRICE,
                QuoteItem::ICMS,
                QuoteItem::IPI,
            ]);
            $itemData[QuoteItem::UNIT_PRICE] = 0;

            $newQuote->items()->create($itemData);
        }

        QuoteAnalysisAction::query()->create([
            QuoteAnalysisAction::QUOTE_ID => $newQuote->id,
            QuoteAnalysisAction::QUOTE_NUMBER => $quoteNumber,
            QuoteAnalysisAction::ACTION => QuoteAnalysisActionEnum::NEW_SUPPLIER,
        ]);

        QuoteCreatedEvent::dispatch($newQuote->id);
    }
}
