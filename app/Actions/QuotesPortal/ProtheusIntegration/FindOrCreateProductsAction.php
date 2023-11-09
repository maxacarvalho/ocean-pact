<?php

namespace App\Actions\QuotesPortal\ProtheusIntegration;

use App\Data\QuotesPortal\QuoteData;
use App\Data\QuotesPortal\QuoteItemData;
use App\Models\QuotesPortal\Product;

class FindOrCreateProductsAction
{
    public function handle(QuoteData $data): array
    {
        $products = [];

        /** @var QuoteItemData $item */
        foreach ($data->items as $item) {
            /** @var Product $newProduct */
            $newProduct = Product::query()->firstOrCreate([
                Product::COMPANY_CODE => $data->company_code,
                Product::COMPANY_CODE_BRANCH => $data->company_code_branch,
                Product::CODE => $item->product->code,
                Product::DESCRIPTION => $item->product->description,
                Product::MEASUREMENT_UNIT => $item->product->measurement_unit,
            ]);

            $products[$item->product->code] = $newProduct->id;
        }

        return $products;
    }
}
