<?php

namespace App\Http\Controllers\QuotesPortal\API;

use App\Actions\QuotesPortal\CreateOrUpdatePurchaseRequestAction;
use App\Data\QuotesPortal\PurchaseRequestData;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuotesPortal\StoreOrUpdatePurchaseRequestData;
use App\Jobs\PurchaseRequestReceivedJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class StoreOrUpdatePurchaseRequestController extends Controller
{
    public function __invoke(
        StoreOrUpdatePurchaseRequestData $request,
        CreateOrUpdatePurchaseRequestAction $createOrUpdatePurchaseRequestAction
    ): PurchaseRequestData|JsonResponse {
        try {
            $purchaseRequest = $createOrUpdatePurchaseRequestAction->handle($request);

            PurchaseRequestReceivedJob::dispatch($purchaseRequest->id);

            return PurchaseRequestData::from($purchaseRequest);
        } catch (Throwable $exception) {
            return response()->json([
                'title' => __('general.unexpected_error'),
                'errors' => [$exception->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
