<?php

namespace App\Actions\QuotesPortal;

use App\Enums\QuotesPortal\QuoteAnalysisActionEnum;
use App\Enums\QuotesPortal\QuoteStatusEnum;
use App\Events\QuotePortal\QuoteProposalCreatedEvent;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteAnalysisAction;
use App\Models\QuotesPortal\QuoteItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class RequestNewProposalAction
{
    public function handle(int $quoteId): void
    {
        /** @var Quote $quote */
        $quote = Quote::query()->with(Quote::RELATION_ITEMS)->findOrFail($quoteId);

        try {
            DB::beginTransaction();

            $newQuote = $quote->replicate();

            $newQuote->forceFill([
                Quote::PROPOSAL_NUMBER => ((int) $quote->proposal_number) + 1,
                Quote::STATUS => QuoteStatusEnum::PENDING,
                Quote::CREATED_AT => now(),
                Quote::UPDATED_AT => now(),
            ])->save();

            /** @var QuoteItem $item */
            foreach ($quote->items as $item) {
                $itemData = $item->toArray();

                unset(
                    $itemData[QuoteItem::ID],
                    $itemData[QuoteItem::QUOTE_ID],
                    $itemData[QuoteItem::CREATED_AT],
                    $itemData[QuoteItem::UPDATED_AT]
                );

                $newQuote->items()->create($itemData);
            }

            Quote::query()
                ->where(Quote::ID, '=', $quoteId)
                ->update([
                    Quote::STATUS => QuoteStatusEnum::REPLACED,
                    Quote::REPLACED_BY => $newQuote->id,
                ]);

            QuoteAnalysisAction::query()->create([
                QuoteAnalysisAction::QUOTE_ID => $newQuote->id,
                QuoteAnalysisAction::QUOTE_NUMBER => $newQuote->quote_number,
                QuoteAnalysisAction::ACTION => QuoteAnalysisActionEnum::NEW_PROPOSAL,
            ]);

            QuoteProposalCreatedEvent::dispatch($newQuote->id);

            DB::commit();

        } catch (Throwable $exception) {
            Log::error('RequestNewProposalAction: Failed to create a new quote proposal', [
                'exception_message' => $exception->getMessage(),
            ]);

            DB::rollBack();

            throw $exception;
        }
    }
}
