<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Enums\BudgetStatusEnum;
use App\Filament\Resources\BudgetResource;
use App\Models\Budget;
use App\Models\Company;
use App\Utils\Str;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as DbQueryBuilder;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListBudgets extends ListRecords
{
    protected static string $resource = BudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ExportAction::make()
                ->label(Str::ucfirst(__('actions.export')))
                ->icon('far-download')
                ->exports([
                    ExcelExport::make()->fromTable()
                        ->withColumns([
                            Column::make(Budget::STATUS)
                                ->heading(Str::formatTitle(__('budget.status')))
                                ->formatStateUsing(function (BudgetStatusEnum $state) {
                                    return $state->getLabel();
                            })
                    ])
                    ->queue()
                ])
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
                Budget::TABLE_NAME.'.'.Budget::ID,
                Budget::TABLE_NAME.'.'.Budget::COMPANY_CODE,
                Budget::TABLE_NAME.'.'.Budget::COMPANY_CODE_BRANCH,
                Budget::TABLE_NAME.'.'.Budget::BUDGET_NUMBER,
                Budget::TABLE_NAME.'.'.Budget::STATUS,
                Budget::TABLE_NAME.'.'.Budget::CREATED_AT,
                Budget::TABLE_NAME.'.'.Budget::UPDATED_AT,
            ])
            ->addSelect([
                'company_name' => fn (DbQueryBuilder $query) => $query->select(Company::BUSINESS_NAME)
                    ->from(Company::TABLE_NAME)
                    ->whereColumn(
                        Company::TABLE_NAME.'.'.Company::CODE,
                        '=',
                        Budget::TABLE_NAME.'.'.Budget::COMPANY_CODE
                    )
                    ->limit(1),
                'company_branch' => fn (DbQueryBuilder $query) => $query->select(Company::BRANCH)
                    ->from(Company::TABLE_NAME)
                    ->whereColumn(
                        Company::TABLE_NAME.'.'.Company::CODE,
                        '=',
                        Budget::TABLE_NAME.'.'.Budget::COMPANY_CODE
                    )
                    ->whereColumn(
                        Company::TABLE_NAME.'.'.Company::CODE_BRANCH,
                        '=',
                        Budget::TABLE_NAME.'.'.Budget::COMPANY_CODE_BRANCH
                    )
                    ->limit(1),
            ]);
    }
}
