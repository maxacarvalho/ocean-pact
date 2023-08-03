<?php

namespace App\Http\Controllers\API\Quote;

use App\Enums\QuoteStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\MarkQuoteAsAcceptedRequest;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Utils\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class MarkQuoteAsAcceptedController extends Controller
{
    public function __invoke(Quote $quote, MarkQuoteAsAcceptedRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $items = collect($request->validated('ITENS'))->pluck('ITEM');

            $quotesExceptTheOneBeingAccepted = $quote
                ->budget
                ->quotes()
                ->where(Quote::ID, '!=', $quote->id)
                ->pluck(Quote::ID)
                ->toArray();

            $quote->items()
                ->whereIn(QuoteItem::ITEM, $items)
                ->update([
                    QuoteItem::STATUS => QuoteStatusEnum::ACCEPTED(),
                ]);

            QuoteItem::query()
                ->whereIn(QuoteItem::QUOTE_ID, $quotesExceptTheOneBeingAccepted)
                ->whereIn(QuoteItem::ITEM, $items)
                ->update([
                    QuoteItem::STATUS => QuoteStatusEnum::REJECTED(),
                ]);

            DB::commit();

            return response()->json([
                'message' => Str::ucfirst(__('quote_item.items_marked_as_accepted')),
            ]);
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('MarkQuoteAsAcceptedController: could not mark quote items as accepted', [
                'quote_id' => $quote->id,
                'exception_message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => Str::ucfirst(__('quote_item.problem_marking_quote_items_as_accepted')),
                'exception_message' => $exception->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
