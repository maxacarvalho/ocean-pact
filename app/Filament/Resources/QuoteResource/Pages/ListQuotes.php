<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Models\Company;
use App\Models\Quote;
use Filament\Pages\Actions\CreateAction as PageCreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as DbQueryBuilder;

class ListQuotes extends ListRecords
{
    protected static string $resource = QuoteResource::class;

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
                Quote::TABLE_NAME.'.'.Quote::ID,
                Quote::TABLE_NAME.'.'.Quote::COMPANY_CODE,
                Quote::TABLE_NAME.'.'.Quote::COMPANY_CODE_BRANCH,
                Quote::TABLE_NAME.'.'.Quote::BUDGET_ID,
                Quote::TABLE_NAME.'.'.Quote::SUPPLIER_ID,
                Quote::TABLE_NAME.'.'.Quote::PAYMENT_CONDITION_ID,
                Quote::TABLE_NAME.'.'.Quote::BUYER_ID,
                Quote::TABLE_NAME.'.'.Quote::QUOTE_NUMBER,
                Quote::TABLE_NAME.'.'.Quote::VALID_UNTIL,
                Quote::TABLE_NAME.'.'.Quote::STATUS,
                Quote::TABLE_NAME.'.'.Quote::COMMENTS,
                Quote::TABLE_NAME.'.'.Quote::CREATED_AT,
                Quote::TABLE_NAME.'.'.Quote::UPDATED_AT,
            ])
            ->addSelect([
                'company_name' => fn (DbQueryBuilder $query) => $query->select(Company::BUSINESS_NAME)
                    ->from(Company::TABLE_NAME)
                    ->whereColumn(
                        Company::TABLE_NAME.'.'.Company::CODE,
                        '=',
                        Quote::TABLE_NAME.'.'.Quote::COMPANY_CODE
                    )
                    ->limit(1),
                'company_branch' => fn (DbQueryBuilder $query) => $query->select(Company::BRANCH)
                    ->from(Company::TABLE_NAME)
                    ->whereColumn(
                        Company::TABLE_NAME.'.'.Company::CODE,
                        '=',
                        Quote::TABLE_NAME.'.'.Quote::COMPANY_CODE
                    )
                    ->whereColumn(
                        Company::TABLE_NAME.'.'.Company::CODE_BRANCH,
                        '=',
                        Quote::TABLE_NAME.'.'.Quote::COMPANY_CODE_BRANCH
                    )
                    ->limit(1),
            ]);
    }
}
