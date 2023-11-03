<?php

namespace App\Actions\QuotesPortal\ProtheusIntegration;

use App\Data\QuotesPortal\Quote\ProtheusQuotePayloadData;
use App\Models\QuotesPortal\Product;

class FindOrCreateProductsAction
{
    public function handle(ProtheusQuotePayloadData $data): array
    {
        $products = [];

        foreach ($data->getProducts() as $product) {
            /** @var Product $newProduct */
            $newProduct = Product::query()->firstOrCreate([
                Product::COMPANY_CODE => $data->EMPRESA,
                Product::COMPANY_CODE_BRANCH => $data->FILIAL,
                Product::CODE => $product->CODIGO,
                Product::DESCRIPTION => $product->DESCRICAO,
                Product::MEASUREMENT_UNIT => $product->UNIDADE_MEDIDA,
            ]);

            $products[$product->CODIGO] = $newProduct->id;
        }

        return $products;
    }
}
