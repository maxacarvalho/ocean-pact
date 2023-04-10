<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Models\Company;
use App\Models\Quote;
use Filament\Pages\Actions\CreateAction as PageCreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

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
                Quote::TABLE_NAME.'.*',
                Company::TABLE_NAME.'.'.Company::CODE_BRANCH,
                Company::TABLE_NAME.'.'.Company::BRANCH,
            ])
            ->join(
                Company::TABLE_NAME,
                Company::TABLE_NAME.'.'.Company::ID,
                '=',
                Quote::TABLE_NAME.'.'.Quote::COMPANY_ID
            );
    }
}
