<?php

namespace App\Http\Controllers\QuotesPortal\API;

use App\Actions\QuotesPortal\CreatePurchaseRequestAction;
use App\Data\QuotesPortal\PurchaseRequestRequestData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseRequestRequest;

class StorePurchaseRequestController extends Controller
{
    public function __invoke(
        StorePurchaseRequestRequest $request,
        CreatePurchaseRequestAction $createPurchaseRequestAction
    ): PurchaseRequestRequestData {
        $purchaseRequest = $createPurchaseRequestAction->handle(
            PurchaseRequestRequestData::from($request->validated())
        );

        return PurchaseRequestRequestData::from($purchaseRequest);
    }
}
