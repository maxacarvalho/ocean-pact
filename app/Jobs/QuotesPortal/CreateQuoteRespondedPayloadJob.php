<?php

namespace App\Jobs\QuotesPortal;

use App\Data\QuotesPortal\QuoteData;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookServer\WebhookCall;

class CreateQuoteRespondedPayloadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $quoteId
    ) {
    }

    public function handle(): void
    {
        /** @var Quote $quote */
        $quote = Quote::query()
            ->with([
                Quote::RELATION_BUDGET,
                Quote::RELATION_COMPANY,
                Quote::RELATION_SUPPLIER,
                Quote::RELATION_PAYMENT_CONDITION,
                Quote::RELATION_BUYER,
                Quote::RELATION_CURRENCY,
                Quote::RELATION_ITEMS => [
                    QuoteItem::RELATION_PRODUCT,
                ],
            ])
            ->findOrFail($this->quoteId);

        if (! $quote->isResponded()) {
            $this->delete();
        }

        if (null === $quote->buyer) {
            Log::warning('Quote without buyer', [
                'quote_id' => $quote->id,
            ]);

            $this->delete();
        }

        $quoteData = QuoteData::from($quote);

        WebhookCall::create()
            ->url(config('integra-hub.base_url').'/integra-hub/webhooks/payload?integration-type-code=cotacoes-respondidas')
            ->payload($quoteData->toArray())
            ->useSecret(config('integra-hub.webhook-secret'))
            ->dispatchSync();
    }
}
