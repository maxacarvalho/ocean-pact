<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\ProductData;
use App\Models\QuotesPortal\Product;

class UpdateProductAction
{
    public function handle(Product $product, ProductData $productData): Product
    {
        $product->update(
            $productData->except(Product::ID, Product::CREATED_AT, Product::UPDATED_AT)->toArray()
        );

        return $product;
    }
}
