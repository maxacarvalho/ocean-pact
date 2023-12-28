<?php

namespace App\Http\Controllers\QuotesPortal\API\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuotesPortal\Product\MassCreateOrUpdateProductsRequest;
use App\Jobs\QuotesPortal\MassCreateOrUpdateProductPayloadProcessorJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class MassCreateOrUpdateProductsController extends Controller
{
    public function __invoke(MassCreateOrUpdateProductsRequest $request): JsonResponse
    {
        $productsPayload = $request->validated('products');

        foreach ($productsPayload as $productPayload) {
            MassCreateOrUpdateProductPayloadProcessorJob::dispatch($productPayload);
        }

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
