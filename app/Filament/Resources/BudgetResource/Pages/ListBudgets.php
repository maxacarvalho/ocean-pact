<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Enums\BudgetStatusEnum;
use App\Filament\Resources\BudgetResource;
use App\Models\Quote;
use App\Utils\Str;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
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
            ExportAction::make()->exports([
                ExcelExport::make()->fromTable()
                    ->withFilename(fn ($resource) => Str::slug($resource::getPluralModelLabel()).'-'.now()->format('Y-m-d'))
                    ->withColumns([
                        Column::make(Quote::STATUS)
                            ->formatStateUsing(fn (BudgetStatusEnum $state) => $state->getLabel()),
                    ]),
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
}
