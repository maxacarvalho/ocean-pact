<?php

namespace App\Http\Controllers\API\Quote;

use App\Enums\QuoteStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Utils\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class MarkQuoteAsAcceptedController extends Controller
{
    public function __invoke(Quote $quote): JsonResponse
    {
        try {
            DB::beginTransaction();

            $budget = $quote->budget;

            $quote->markAsAccepted();

            $budget->quotes()->where(Quote::ID, '!=', $quote->id)->update([
                'status' => QuoteStatusEnum::REJECTED(),
            ]);

            $budget->markAsClosed();

            DB::commit();

            return response()->json([
                'message' => Str::ucfirst(__('quote.quote_marked_as_accepted')),
            ]);
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('MarkQuoteAsAcceptedController: could not mark quote as accepted', [
                'quote_id' => $quote->id,
                'exception_message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => Str::ucfirst(__('quote.problem_marking_quote_as_accepted')),
                'exception_message' => $exception->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
