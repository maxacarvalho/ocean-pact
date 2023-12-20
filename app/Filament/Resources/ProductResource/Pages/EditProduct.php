<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\QuotesPortal\Product;
use App\Utils\Money;
use Filament\Actions\DeleteAction as PageDeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PageDeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Money $lastPrice */
        $lastPrice = $data['last_price'];
        $data[Product::LAST_PRICE.'_currency'] = $lastPrice->currency;

        /** @var Money $smallestPrice */
        $smallestPrice = $data['smallest_price'];
        $data[Product::SMALLEST_PRICE.'_currency'] = $smallestPrice->currency;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
