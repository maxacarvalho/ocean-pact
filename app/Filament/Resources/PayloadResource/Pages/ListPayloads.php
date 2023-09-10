<?php

namespace App\Filament\Resources\PayloadResource\Pages;

use App\Filament\Resources\PayloadResource;
use Filament\Actions\CreateAction as PageCreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPayloads extends ListRecords
{
    protected static string $resource = PayloadResource::class;

    protected function getHeaderActions(): array
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
