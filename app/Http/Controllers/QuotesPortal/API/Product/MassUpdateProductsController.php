<?php

namespace App\Http\Controllers\QuotesPortal\API\Product;

use App\Actions\QuotesPortal\UpdateProductAction;
use App\Data\QuotesPortal\ErrorResponseData;
use App\Data\QuotesPortal\ProductData;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuotesPortal\Product\MassUpdateProductsRequest;
use App\Models\QuotesPortal\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class MassUpdateProductsController extends Controller
{
    public function __construct(
        private readonly UpdateProductAction $updateProductAction
    ) {
        //
    }

    public function __invoke(MassUpdateProductsRequest $request): JsonResponse
    {
        $productsPayload = $request->validated('products');

        $updatedProducts = [];
        $errors = [];

        foreach ($productsPayload as $productPayload) {
            try {
                $productData = ProductData::from($productPayload);

                /** @var Product $product */
                $product = Product::query()
                    ->where(Product::CODE, '=', $productData->code)
                    ->where(Product::COMPANY_CODE, '=', $productData->company_code)
                    ->where(Product::COMPANY_CODE_BRANCH, '=', $productData->company_code_branch)
                    ->firstOrFail();

                $product = $this->updateProductAction->handle(
                    $product,
                    $productData->except(Product::CODE, Product::COMPANY_CODE, Product::COMPANY_CODE_BRANCH)
                );

                $updatedProducts[] = $product->id;
            } catch (Throwable $exception) {
                Log::error('UpdateProductController unexpected exception', [
                    'namespace' => __CLASS__,
                    'exception_message' => $exception->getMessage(),
                    'context' => [
                        'productPayload' => $productPayload,
                        'request' => $request->validated(),
                    ],
                ]);

                $responseError = ErrorResponseData::from([
                    'title' => __('product.error_messages.error_updating_product'),
                    'errors' => [$exception->getMessage()],
                ]);

                $errors[] = $responseError->toArray();
            }
        }

        $products = Product::query()
            ->whereIn('id', $updatedProducts)
            ->get();

        $response = [
            'updated' => ProductData::collection($products),
        ];

        if (! empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response);
    }
}
