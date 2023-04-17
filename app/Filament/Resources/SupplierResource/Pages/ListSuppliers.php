<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use App\Models\Company;
use App\Models\Supplier;
use Filament\Pages\Actions\CreateAction as PageCreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

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
                Supplier::TABLE_NAME.'.*',
                Company::TABLE_NAME.'.'.Company::CODE_BRANCH,
                Company::TABLE_NAME.'.'.Company::BRANCH,
            ])
            ->leftJoin(
                Company::TABLE_NAME,
                Company::TABLE_NAME.'.'.Company::ID,
                '=',
                Supplier::TABLE_NAME.'.'.Supplier::COMPANY_ID
            );
    }
}
