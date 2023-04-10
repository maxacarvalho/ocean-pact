<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use App\Models\Budget;
use App\Models\Company;
use Filament\Pages\Actions\CreateAction as PageCreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListBudgets extends ListRecords
{
    protected static string $resource = BudgetResource::class;

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
                Budget::TABLE_NAME.'.*',
                Company::TABLE_NAME.'.'.Company::CODE_BRANCH,
                Company::TABLE_NAME.'.'.Company::BRANCH,
            ])
            ->join(
                Company::TABLE_NAME,
                Company::TABLE_NAME.'.'.Company::ID,
                '=',
                Budget::TABLE_NAME.'.'.Budget::COMPANY_ID
            );
    }
}
