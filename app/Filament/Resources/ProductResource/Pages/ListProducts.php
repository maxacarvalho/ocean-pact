<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Company;
use App\Models\Product;
use App\Utils\Str;
use Filament\Actions\CreateAction as PageCreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as DbQueryBuilder;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PageCreateAction::make(),
            ExportAction::make()->exports([
                ExcelExport::make()->fromTable()
                    ->withFilename(fn ($resource) => Str::slug($resource::getPluralModelLabel()).'-'.now()->format('Y-m-d')),
            ]),
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
                Product::TABLE_NAME.'.'.Product::ID,
                Product::TABLE_NAME.'.'.Product::COMPANY_CODE,
                Product::TABLE_NAME.'.'.Product::COMPANY_CODE_BRANCH,
                Product::TABLE_NAME.'.'.Product::CODE,
                Product::TABLE_NAME.'.'.Product::DESCRIPTION,
                Product::TABLE_NAME.'.'.Product::MEASUREMENT_UNIT,
                Product::TABLE_NAME.'.'.Product::CREATED_AT,
                Product::TABLE_NAME.'.'.Product::UPDATED_AT,
            ])
            ->addSelect([
                'company_name' => fn (DbQueryBuilder $query) => $query->select(Company::BUSINESS_NAME)
                    ->from(Company::TABLE_NAME)
                    ->whereColumn(
                        Company::TABLE_NAME.'.'.Company::CODE,
                        '=',
                        Product::TABLE_NAME.'.'.Product::COMPANY_CODE
                    )
                    ->limit(1),
                'company_branch' => fn (DbQueryBuilder $query) => $query->select(Company::BRANCH)
                    ->from(Company::TABLE_NAME)
                    ->whereColumn(
                        Company::TABLE_NAME.'.'.Company::CODE,
                        '=',
                        Product::TABLE_NAME.'.'.Product::COMPANY_CODE
                    )
                    ->whereColumn(
                        Company::TABLE_NAME.'.'.Company::CODE_BRANCH,
                        '=',
                        Product::TABLE_NAME.'.'.Product::COMPANY_CODE_BRANCH
                    )
                    ->limit(1),
            ]);
    }
}
