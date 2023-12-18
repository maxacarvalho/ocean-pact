<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\ProductData;
use App\Models\QuotesPortal\Product;

class CreateProductAction
{
    public function handle(ProductData $productData): Product
    {
        /** @var Product $product */
        $product = Product::query()->create($productData->toArray());

        return $product;
    }
}
