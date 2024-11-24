<?php

namespace App\Jobs\QuotesPortal;

use App\Actions\QuotesPortal\CreateProductAction;
use App\Actions\QuotesPortal\UpdateProductAction;
use App\Data\QuotesPortal\ProductData;
use App\Models\QuotesPortal\Product;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class MassCreateOrUpdateProductPayloadProcessorJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly array $productPayload,
    ) {
        //
    }

    public function handle(
        CreateProductAction $createProductAction,
        UpdateProductAction $updateProductAction
    ): void {
        $productData = ProductData::from($this->productPayload);

        /** @var Product|null $product */
        $product = Product::query()
            ->where(Product::CODE, '=', $productData->code)
            ->where(Product::COMPANY_CODE, '=', $productData->company_code)
            ->where(Product::COMPANY_CODE_BRANCH, '=', $productData->company_code_branch)
            ->first();

        if ($product === null) {
            $createProductAction->handle($productData);
        } else {
            $updateProductAction->handle(
                $product,
                $productData->except(Product::CODE, Product::COMPANY_CODE, Product::COMPANY_CODE_BRANCH)
            );
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('MassCreateOrUpdateProductPayloadProcessorJob exception', [
            'exception_message' => $exception->getMessage(),
            'product_payload' => $this->productPayload,
        ]);

        report($exception);
    }
}
