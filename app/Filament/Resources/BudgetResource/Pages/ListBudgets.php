<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use App\Models\Budget;
use App\Models\Company;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as DbQueryBuilder;

class ListBudgets extends ListRecords
{
    protected static string $resource = BudgetResource::class;

    protected function getHeaderActions() : array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function shouldPersistTableFiltersInSession() : bool
    {
        return true;
    }

    protected function shouldPersistTableSortInSession() : bool
    {
        return true;
    }

    protected function getTableQuery() : Builder
    {
        return parent::getTableQuery()
            ->select([
                Budget::TABLE_NAME . '.' . Budget::ID,
                Budget::TABLE_NAME . '.' . Budget::COMPANY_CODE,
                Budget::TABLE_NAME . '.' . Budget::COMPANY_CODE_BRANCH,
                Budget::TABLE_NAME . '.' . Budget::BUDGET_NUMBER,
                Budget::TABLE_NAME . '.' . Budget::STATUS,
                Budget::TABLE_NAME . '.' . Budget::CREATED_AT,
                Budget::TABLE_NAME . '.' . Budget::UPDATED_AT,
            ])
            ->addSelect([
                'company_name' => fn (DbQueryBuilder $query) => $query->select(Company::BUSINESS_NAME)
                    ->from(Company::TABLE_NAME)
                    ->whereColumn(
                        Company::TABLE_NAME . '.' . Company::CODE,
                        '=',
                        Budget::TABLE_NAME . '.' . Budget::COMPANY_CODE
                    )
                    ->limit(1),
                'company_branch' => fn (DbQueryBuilder $query) => $query->select(Company::BRANCH)
                    ->from(Company::TABLE_NAME)
                    ->whereColumn(
                        Company::TABLE_NAME . '.' . Company::CODE,
                        '=',
                        Budget::TABLE_NAME . '.' . Budget::COMPANY_CODE
                    )
                    ->whereColumn(
                        Company::TABLE_NAME . '.' . Company::CODE_BRANCH,
                        '=',
                        Budget::TABLE_NAME . '.' . Budget::COMPANY_CODE_BRANCH
                    )
                    ->limit(1),
            ]);
    }
}