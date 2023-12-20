<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\QuotesPortal\Product;
use App\Utils\Money;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $lastPrice = Money::ofFormatted($data[Product::LAST_PRICE.'_currency'], $data['last_price']);
        $data[Product::LAST_PRICE] = [
            'currency' => $lastPrice->currency,
            'amount' => $lastPrice->getMinorAmount(),
        ];

        $smallestPrice = Money::ofFormatted($data[Product::SMALLEST_PRICE.'_currency'], $data['smallest_price']);
        $data[Product::SMALLEST_PRICE] = [
            'currency' => $smallestPrice->currency,
            'amount' => $smallestPrice->getMinorAmount(),
        ];

        unset($data[Product::LAST_PRICE.'_currency'], $data[Product::SMALLEST_PRICE.'_currency']);

        return $data;
    }
}
