<?php

namespace App\Filament\Resources\IntegrationTypeResource\Pages;

use App\Filament\Resources\IntegrationTypeResource;
use Filament\Pages\Actions\CreateAction as PageCreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIntegrationTypes extends ListRecords
{
    protected static string $resource = IntegrationTypeResource::class;

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
}
