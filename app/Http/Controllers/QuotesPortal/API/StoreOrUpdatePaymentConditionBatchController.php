<?php

namespace App\Http\Controllers\QuotesPortal\API;

use App\Actions\IntegraHub\UpdateOrCreatePaymentConditionAction;
use App\Data\QuotesPortal\PaymentConditionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuotesPortal\StoreOrUpdatePaymentConditionBatchRequest;
use Illuminate\Http\Response;
use Throwable;

class StoreOrUpdatePaymentConditionBatchController extends Controller
{
    public function __invoke(
        StoreOrUpdatePaymentConditionBatchRequest $request,
        UpdateOrCreatePaymentConditionAction $updateOrCreatePaymentConditionAction
    ) {
        $payload = $request->validated();

        try {
            $paymentConditionInputCollection = PaymentConditionData::collection($payload);

            $updatedOrCreatedPaymentConditions = [];

            /** @var PaymentConditionData $paymentConditionInput */
            foreach ($paymentConditionInputCollection as $paymentConditionInput) {
                $updatedOrCreatedPaymentConditions[] = $updateOrCreatePaymentConditionAction->handle($paymentConditionInput);
            }

            return PaymentConditionData::collection($updatedOrCreatedPaymentConditions);
        } catch (Throwable $exception) {
            return response()->json([
                'title' => __('general.unexpected_error'),
                'errors' => [$exception->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
