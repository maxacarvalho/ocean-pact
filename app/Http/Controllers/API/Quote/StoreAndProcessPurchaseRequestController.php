<?php

namespace App\Http\Controllers\API\Quote;

use App\Data\PurchaseRequestRequestData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAndProcessPurchaseRequestRequest;
use App\Jobs\PurchaseRequestReceivedJob;

class StoreAndProcessPurchaseRequestController extends Controller
{
    public function __invoke(StoreAndProcessPurchaseRequestRequest $request): void
    {
        PurchaseRequestReceivedJob::dispatchAfterResponse(
            PurchaseRequestRequestData::from($request->validated())
        );
    }
}
