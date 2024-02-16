<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\StoreQuotePayloadData;
use App\Models\QuotesPortal\Product;

class FindOrCreateProductsAction
{
    /** @return array<string, int> */
    public function handle(StoreQuotePayloadData $data): array
    {
        $mappingCodesToProductsIds = [];

        foreach ($data->items as $item) {
            /** @var Product $product */
            $product = Product::query()->firstOrCreate(
                [
                    Product::COMPANY_CODE => $data->companyCode,
                    Product::COMPANY_CODE_BRANCH => $data->companyCodeBranch,
                    Product::CODE => $item->product->code,
                ],
                [
                    Product::DESCRIPTION => $item->product->description,
                    Product::MEASUREMENT_UNIT => $item->product->measurementUnit,
                    Product::SMALLEST_PRICE => [
                        'currency' => $item->product->smallestPrice->currency,
                        'amount' => $item->product->smallestPrice->getMinorAmount(),
                    ],
                    Product::LAST_PRICE => [
                        'currency' => $item->product->lastPrice->currency,
                        'amount' => $item->product->lastPrice->getMinorAmount(),
                    ],
                    Product::SMALLEST_ETA => $item->product->smallestEta,
                ]
            );

            $mappingCodesToProductsIds[$product->code] = $product->id;
        }

        return $mappingCodesToProductsIds;
    }
}
