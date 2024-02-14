<?php

namespace App\Http\Controllers\QuotesPortal\API;

use App\Actions\IntegraHub\UpdateOrCreatePaymentConditionAction;
use App\Data\QuotesPortal\PaymentConditionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuotesPortal\StoreOrUpdatePaymentConditionBatchRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Spatie\LaravelData\CursorPaginatedDataCollection;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;
use Throwable;

class StoreOrUpdatePaymentConditionBatchController extends Controller
{
    public function __invoke(
        StoreOrUpdatePaymentConditionBatchRequest $request,
        UpdateOrCreatePaymentConditionAction $updateOrCreatePaymentConditionAction
    ): JsonResponse|PaginatedDataCollection|CursorPaginatedDataCollection|DataCollection {
        $payload = $request->validated();

        try {
            $paymentConditionInputCollection = PaymentConditionData::collect($payload);

            $updatedOrCreatedPaymentConditions = [];

            /** @var PaymentConditionData $paymentConditionInput */
            foreach ($paymentConditionInputCollection as $paymentConditionInput) {
                $updatedOrCreatedPaymentConditions[] = $updateOrCreatePaymentConditionAction->handle($paymentConditionInput);
            }

            return PaymentConditionData::collect($updatedOrCreatedPaymentConditions);
        } catch (Throwable $exception) {
            return response()->json([
                'title' => __('general.unexpected_error'),
                'errors' => [$exception->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
