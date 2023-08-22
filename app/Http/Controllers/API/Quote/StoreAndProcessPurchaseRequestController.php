<?php

namespace App\Http\Controllers\API\Quote;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAndProcessPurchaseRequestRequest;
use App\Models\PurchaseRequest;

class StoreAndProcessPurchaseRequestController extends Controller
{
    public function __invoke(StoreAndProcessPurchaseRequestRequest $request): void
    {
        PurchaseRequest::query()->create([
            PurchaseRequest::QUOTE_ID => $request->input('ID_COTACAO'),
            PurchaseRequest::PURCHASE_REQUEST_NUMBER => $request->input('NUMERO_PEDIDO'),
            PurchaseRequest::FILE => $request->input('ARQUIVO'),
        ]);
    }
}
