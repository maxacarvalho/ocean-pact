<?php

namespace App\Http\Controllers\QuotesPortal\API\Product;

use App\Actions\QuotesPortal\UpdateProductAction;
use App\Data\QuotesPortal\ErrorResponseData;
use App\Data\QuotesPortal\ProductData;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuotesPortal\Product\UpdateProductRequest;
use App\Models\QuotesPortal\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateProductController extends Controller
{
    public function __construct(
        private readonly UpdateProductAction $updateProductAction
    ) {
        //
    }

    public function __invoke(
        string $code,
        string $companyCode,
        ?string $companyCodeBranch,
        UpdateProductRequest $request
    ): JsonResponse|ProductData {
        try {
            /** @var Product $product */
            $product = Product::query()
                ->where(Product::CODE, '=', $code)
                ->where(Product::COMPANY_CODE, '=', $companyCode)
                ->where(Product::COMPANY_CODE_BRANCH, '=', $companyCodeBranch)
                ->firstOrFail();

            $productData = ProductData::from($request->validated());

            $product = $this->updateProductAction->handle($product, $productData);

            return ProductData::from($product);
        } catch (Throwable $exception) {
            Log::error('UpdateProductController unexpected exception', [
                'namespace' => __CLASS__,
                'exception_message' => $exception->getMessage(),
                'context' => [
                    'code' => $code,
                    'company_code' => $companyCode,
                    'company_code_branch' => $companyCodeBranch,
                    'request' => $request->validated(),
                ],
            ]);

            $responseError = ErrorResponseData::from([
                'title' => __('product.error_messages.error_updating_product'),
                'errors' => [$exception->getMessage()],
            ]);

            return response()->json($responseError->toArray(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
