<?php

namespace App\Http\Controllers\QuotesPortal\API;

use App\Actions\QuotesPortal\ProcessQuotePayloadAction;
use App\Data\QuotesPortal\ErrorResponseData;
use App\Data\QuotesPortal\QuoteData;
use App\Data\QuotesPortal\StoreQuotePayloadData;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuotesPortal\StoreQuoteRequest;
use App\Models\QuotesPortal\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class StoreQuoteController extends Controller
{
    public function __construct(
        private readonly ProcessQuotePayloadAction $processQuotePayloadAction
    ) {
        //
    }

    public function __invoke(StoreQuoteRequest $request): JsonResponse|Collection
    {
        try {
            $quotePayloadData = StoreQuotePayloadData::from($request);

            $quoteIds = $this->processQuotePayloadAction->handle($quotePayloadData);

            $quoteCollection = Quote::query()
                ->with(Quote::RELATION_ITEMS)
                ->whereIn(Quote::ID, $quoteIds)
                ->get();

            return QuoteData::collect($quoteCollection);
        } catch (Throwable $exception) {
            Log::error('StoreQuoteController unexpected exception', [
                'namespace' => __CLASS__,
                'exception_message' => $exception->getMessage(),
                'context' => [
                    'request' => $request->validated(),
                ],
            ]);

            $responseError = ErrorResponseData::from([
                'title' => __('quote.error_messages.error_creating_quote'),
                'errors' => [$exception->getMessage()],
            ]);

            report($exception);

            return response()->json($responseError->toArray(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
