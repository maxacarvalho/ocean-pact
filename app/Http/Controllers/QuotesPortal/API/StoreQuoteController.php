<?php

namespace App\Http\Controllers\QuotesPortal\API;

use App\Actions\QuotesPortal\ProtheusIntegration\ProcessQuotePayloadAction;
use App\Data\QuotesPortal\Quote\ProtheusQuotePayloadData;
use App\Data\QuotesPortal\Quote\StoreQuoteErrorResponseData;
use App\Data\QuotesPortal\QuoteData;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuotesPortal\StoreQuoteRequest;
use Illuminate\Support\Facades\Log;
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
            Log::error('StoreQuoteController unexpected exception', [
                'namespace' => __CLASS__,
                'exception_message' => $exception->getMessage(),
                'context' => [
                    'request' => $request->validated(),
                ],
            ]);

            return StoreQuoteErrorResponseData::from([
                'title' => __('quote.error_messages.error_creating_quote'),
                'errors' => [$exception->getMessage()],
            ]);
        }
    }
}
