<?php

namespace App\Http\Controllers\QuotesPortal\API;

use App\Actions\QuotesPortal\ProcessQuotePayloadAction;
use App\Data\QuotesPortal\QuoteData;
use App\Data\QuotesPortal\StoreQuoteErrorResponseData;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuotesPortal\StoreQuoteRequest;
use App\Models\QuotesPortal\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class StoreQuoteController extends Controller
{
    public function __construct(
        private readonly ProcessQuotePayloadAction $processQuotePayloadAction
    ) {
        //
    }

    public function __invoke(StoreQuoteRequest $request): QuoteData|JsonResponse
    {
        try {
            $quotePayloadData = QuoteData::fromStoreQuoteRequest($request);

            $quote = $this->processQuotePayloadAction->handle($quotePayloadData);
            $quote->load(Quote::RELATION_ITEMS);

            return QuoteData::from($quote);
        } catch (Throwable $exception) {
            Log::error('StoreQuoteController unexpected exception', [
                'namespace' => __CLASS__,
                'exception_message' => $exception->getMessage(),
                'context' => [
                    'request' => $request->validated(),
                ],
            ]);

            $responseError = StoreQuoteErrorResponseData::from([
                'title' => __('quote.error_messages.error_creating_quote'),
                'errors' => [$exception->getMessage()],
            ]);

            return response()->json($responseError->toArray(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
