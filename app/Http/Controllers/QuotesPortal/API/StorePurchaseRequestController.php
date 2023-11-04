<?php

namespace App\Http\Controllers\QuotesPortal\API;

use App\Actions\QuotesPortal\CreatePurchaseRequestAction;
use App\Data\QuotesPortal\PurchaseRequestRequestData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseRequestRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class StorePurchaseRequestController extends Controller
{
    public function __invoke(
        StorePurchaseRequestRequest $request,
        CreatePurchaseRequestAction $createPurchaseRequestAction
    ): PurchaseRequestRequestData|JsonResponse {
        try {
            $purchaseRequest = $createPurchaseRequestAction->handle(
                PurchaseRequestRequestData::from($request->validated())
            );

            return PurchaseRequestRequestData::from($purchaseRequest);
        } catch (Throwable $exception) {
            return response()->json([
                'title' => __('general.unexpected_error'),
                'errors' => [$exception->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
