<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\QuoteData;
use App\Data\QuotesPortal\QuoteItemData;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Product;

class FindOrCreateProductsAction
{
    public function handle(QuoteData $data, Company $company): array
    {
        $products = [];

        /** @var QuoteItemData $item */
        foreach ($data->items as $item) {

            ray($item->toArray());
            ray()->pause();

            /** @var Product $newProduct */
            $newProduct = Product::query()->firstOrCreate([
                Product::COMPANY_CODE => $company->code,
                Product::COMPANY_CODE_BRANCH => $company->code_branch,
                Product::CODE => $item->product->code,
                Product::DESCRIPTION => $item->product->description,
                Product::MEASUREMENT_UNIT => $item->product->measurement_unit,
                Product::SMALLEST_PRICE => [
                    'currency' => $item->product->smallest_price->currency,
                    'amount' => $item->product->smallest_price->getMinorAmount(),
                ],
                Product::LAST_PRICE => [
                    'currency' => $item->product->last_price->currency,
                    'amount' => $item->product->last_price->getMinorAmount(),
                ],
                Product::SMALLEST_ETA => $item->product->smallest_eta,
            ]);

            $products[$item->product->code] = $newProduct->id;
        }

        return $products;
    }
}
