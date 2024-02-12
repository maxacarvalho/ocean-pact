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
        $newQuote->supplier_id = $newSupplierId;
        $newQuote->proposal_number = 1;
        $newQuote->status = QuoteStatusEnum::PENDING;
        $newQuote->save();

        $quoteSample->load(Quote::RELATION_ITEMS);

        /** @var QuoteItem $item */
        foreach ($quoteSample->items as $item) {
            $itemData = array_merge($item->toArray(), [QuoteItem::ID => $newQuote->id]);
            unset($itemData[QuoteItem::ID], $itemData[QuoteItem::CREATED_AT], $itemData[QuoteItem::UPDATED_AT]);

            $newQuote->items()->create($itemData);
        }

        QuoteAnalysisAction::query()->create([
            QuoteAnalysisAction::QUOTE_ID => $newQuote->id,
            QuoteAnalysisAction::ACTION => QuoteAnalysisActionEnum::NEW_SUPPLIER,
        ]);

        QuoteCreatedEvent::dispatch($newQuote->id);
    }
}
