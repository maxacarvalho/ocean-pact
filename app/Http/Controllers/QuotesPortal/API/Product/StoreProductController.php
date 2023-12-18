<?php

namespace App\Http\Controllers\QuotesPortal\API\Product;

use App\Actions\QuotesPortal\CreateProductAction;
use App\Data\QuotesPortal\ErrorResponseData;
use App\Data\QuotesPortal\ProductData;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuotesPortal\Product\StoreProductRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class StoreProductController extends Controller
{
    public function __construct(
        private readonly CreateProductAction $createProductAction
    ) {
        //
    }

    public function __invoke(StoreProductRequest $request): JsonResponse|ProductData
    {
        try {
            $productData = ProductData::from($request->validated());

            $product = $this->createProductAction->handle($productData);

            return ProductData::from($product);
        } catch (Throwable $exception) {
            Log::error('StoreProductController unexpected exception', [
                'namespace' => __CLASS__,
                'exception_message' => $exception->getMessage(),
                'context' => [
                    'request' => $request->validated(),
                ],
            ]);

            $responseError = ErrorResponseData::from([
                'title' => __('product.error_messages.error_creating_product'),
                'errors' => [$exception->getMessage()],
            ]);

            return response()->json($responseError->toArray(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
