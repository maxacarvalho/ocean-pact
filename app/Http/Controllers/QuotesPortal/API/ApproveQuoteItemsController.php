<?php

namespace App\Http\Controllers\QuotesPortal\API;

use App\Enums\QuotesPortal\QuoteItemStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveQuoteItemsRequest;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use App\Utils\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApproveQuoteItemsController extends Controller
{
    public function __invoke(Quote $quote, ApproveQuoteItemsRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $items = $request->validated(Quote::RELATION_ITEMS);

            $quotesExceptTheOneBeingAccepted = $quote
                ->budget
                ->quotes()
                ->where(Quote::ID, '!=', $quote->id)
                ->pluck(Quote::ID)
                ->toArray();

            $quote->items()
                ->whereIn(QuoteItem::ITEM, $items)
                ->update([
                    QuoteItem::STATUS => QuoteItemStatusEnum::ACCEPTED,
                ]);

            $quote->markAsAnalyzed();

            QuoteItem::query()
                ->whereIn(QuoteItem::QUOTE_ID, $quotesExceptTheOneBeingAccepted)
                ->whereIn(QuoteItem::ITEM, $items)
                ->update([
                    QuoteItem::STATUS => QuoteItemStatusEnum::REJECTED,
                ]);

            DB::commit();

            return response()->json([
                'title' => Str::ucfirst(__('quote_item.items_marked_as_accepted')),
            ]);
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('MarkQuoteAsAcceptedController: could not mark quote items as accepted', [
                'namespace' => __CLASS__,
                'exception_message' => $exception->getMessage(),
                'context' => [
                    'quote_id' => $quote->id,
                ],
            ]);

            return response()->json([
                'title' => Str::ucfirst(__('quote_item.problem_marking_quote_items_as_accepted')),
                'errors' => [$exception->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
