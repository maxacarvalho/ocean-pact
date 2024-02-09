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
            /** @var Product|null $product */
            $product = Product::query()
                ->where(Product::COMPANY_CODE, '=', $company->code)
                ->where(Product::COMPANY_CODE_BRANCH, '=', $company->code_branch)
                ->where(Product::CODE, '=', $item->product->code)
                ->first();

            if (null === $product) {
                /** @var Product $product */
                $product = Product::query()->create([
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
            }

            $product->description = $item->product->description;
            $product->measurement_unit = $item->product->measurement_unit;
            $product->smallest_price = [
                'currency' => $item->product->smallest_price->currency,
                'amount' => $item->product->smallest_price->getMinorAmount(),
            ];
            $product->last_price = [
                'currency' => $item->product->last_price->currency,
                'amount' => $item->product->last_price->getMinorAmount(),
            ];
            $product->smallest_eta = $item->product->smallest_eta;
            $product->save();

            $products[$item->product->code] = $product->id;
        }

        return $products;
    }
}
