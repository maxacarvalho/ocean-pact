<?php

namespace App\Http\Controllers\QuotesPortal\API;

use App\Actions\QuotesPortal\ProtheusIntegration\ProcessQuotePayloadAction;
use App\Data\QuotesPortal\Quote\ProtheusQuotePayloadData;
use App\Data\QuotesPortal\Quote\QuoteData;
use App\Data\QuotesPortal\Quote\StoreQuoteErrorResponseData;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuotesPortal\StoreQuoteRequest;
use Throwable;

class StoreQuoteController extends Controller
{
    public function __construct(
        private readonly ProcessQuotePayloadAction $processQuotePayloadAction
    ) {
        //
    }

    public function __invoke(StoreQuoteRequest $request): QuoteData|StoreQuoteErrorResponseData
    {
        try {
            $quotePayloadData = ProtheusQuotePayloadData::from($request->validated());

            $quote = $this->processQuotePayloadAction->handle($quotePayloadData);

            return QuoteData::from($quote);
        } catch (Throwable $exception) {
            return StoreQuoteErrorResponseData::from([
                'title' => __('quote.error_messages.error_creating_quote'),
                'errors' => [$exception->getMessage()],
            ]);
        }
    }
}
