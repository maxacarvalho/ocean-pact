<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use App\Models\Company;
use App\Models\Supplier;
use Filament\Pages\Actions\CreateAction as PageCreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as DbQueryBuilder;

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
                Supplier::TABLE_NAME.'.'.Supplier::ID,
                Supplier::TABLE_NAME.'.'.Supplier::COMPANY_CODE,
                Supplier::TABLE_NAME.'.'.Supplier::COMPANY_CODE_BRANCH,
                Supplier::TABLE_NAME.'.'.Supplier::STORE,
                Supplier::TABLE_NAME.'.'.Supplier::CODE,
                Supplier::TABLE_NAME.'.'.Supplier::NAME,
                Supplier::TABLE_NAME.'.'.Supplier::BUSINESS_NAME,
                Supplier::TABLE_NAME.'.'.Supplier::ADDRESS,
                Supplier::TABLE_NAME.'.'.Supplier::NUMBER,
                Supplier::TABLE_NAME.'.'.Supplier::STATE_CODE,
                Supplier::TABLE_NAME.'.'.Supplier::POSTAL_CODE,
                Supplier::TABLE_NAME.'.'.Supplier::CNPJ_CPF,
                Supplier::TABLE_NAME.'.'.Supplier::PHONE_CODE,
                Supplier::TABLE_NAME.'.'.Supplier::PHONE_NUMBER,
                Supplier::TABLE_NAME.'.'.Supplier::CONTACT,
                Supplier::TABLE_NAME.'.'.Supplier::EMAIL,
                Supplier::TABLE_NAME.'.'.Supplier::CREATED_AT,
                Supplier::TABLE_NAME.'.'.Supplier::UPDATED_AT,
            ])
            ->addSelect([
                'company_name' => fn (DbQueryBuilder $query) => $query->select(Company::BUSINESS_NAME)
                    ->from(Company::TABLE_NAME)
                    ->whereColumn(
                        Company::TABLE_NAME.'.'.Company::CODE,
                        '=',
                        Supplier::TABLE_NAME.'.'.Supplier::COMPANY_CODE
                    )
                    ->limit(1),
                'company_branch' => fn (DbQueryBuilder $query) => $query->select(Company::BRANCH)
                    ->from(Company::TABLE_NAME)
                    ->whereColumn(
                        Company::TABLE_NAME.'.'.Company::CODE,
                        '=',
                        Supplier::TABLE_NAME.'.'.Supplier::COMPANY_CODE
                    )
                    ->whereColumn(
                        Company::TABLE_NAME.'.'.Company::CODE_BRANCH,
                        '=',
                        Supplier::TABLE_NAME.'.'.Supplier::COMPANY_CODE_BRANCH
                    )
                    ->limit(1),
            ]);
    }
}
