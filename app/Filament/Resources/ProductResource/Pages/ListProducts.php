<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Company;
use App\Models\Product;
use Filament\Pages\Actions\CreateAction as PageCreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getActions(): array
    {
        return [
            PageCreateAction::make(),
        ];
    }

    protected function shouldPersistTableFiltersInSession(): bool
    {
        return true;
    }

    protected function shouldPersistTableSortInSession(): bool
    {
        return true;
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->select([
                Product::TABLE_NAME.'.*',
                Company::TABLE_NAME.'.'.Company::CODE_BRANCH,
                Company::TABLE_NAME.'.'.Company::BRANCH,
            ])
            ->join(
                Company::TABLE_NAME,
                Company::TABLE_NAME.'.'.Company::ID,
                '=',
                Product::TABLE_NAME.'.'.Product::COMPANY_ID
            );
    }
}
