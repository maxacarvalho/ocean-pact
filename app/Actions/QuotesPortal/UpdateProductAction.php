<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\ProductData;
use App\Models\QuotesPortal\Product;

class UpdateProductAction
{
    public function handle(Product $product, ProductData $productData): Product
    {
        $product->update($productData->toArray());

        return $product;
    }
}
