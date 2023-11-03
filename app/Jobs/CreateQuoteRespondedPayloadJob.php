<?php

namespace App\Jobs;

use App\Data\QuotesPortal\Quote\Out\ProtheusQuotePayloadData;
use App\Enums\IntegraHub\PayloadProcessingStatusEnum;
use App\Models\IntegraHub\IntegrationType;
use App\Models\IntegraHub\Payload;
use App\Models\QuotesPortal\Product;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
            ->with(
                Quote::RELATION_COMPANY,
                Quote::RELATION_SUPPLIER,
                Quote::RELATION_PAYMENT_CONDITION,
                Quote::RELATION_BUYER,
                Quote::RELATION_BUYER.'.'.User::RELATION_COMPANIES,
                Quote::RELATION_ITEMS,
                Quote::RELATION_ITEMS.'.'.QuoteItem::RELATION_PRODUCT,
                Quote::RELATION_ITEMS.'.'.QuoteItem::RELATION_PRODUCT.'.'.Product::RELATION_COMPANY
            )
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

        $protheusPayloadData = ProtheusQuotePayloadData::fromQuote($quote);

        /** @var IntegrationType $integrationType */
        $integrationType = IntegrationType::query()
            ->where(IntegrationType::CODE, '=', IntegrationType::INTEGRATION_ANSWERED_QUOTES)
            ->firstOrFail();

        $integrationType->payloads()->create([
            Payload::PAYLOAD => $protheusPayloadData->toArray(),
            Payload::PAYLOAD_HASH => $protheusPayloadData->getHash(),
            Payload::STORED_AT => now(),
            Payload::PROCESSING_STATUS => PayloadProcessingStatusEnum::READY,
        ]);
    }
}
