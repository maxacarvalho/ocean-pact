<?php

namespace App\Filament\Resources\IntegrationTypeResource\Pages;

use App\Filament\Resources\IntegrationTypeResource;
use App\Models\IntegraHub\IntegrationType;
use Filament\Actions\CreateAction as PageCreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListIntegrationTypes extends ListRecords
{
    protected static string $resource = IntegrationTypeResource::class;

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

    protected function getTableQuery(): Builder
    {
        if (Auth::user()->isSuperAdmin()) {
            return parent::getTableQuery();
        }

        return parent::getTableQuery()->where(IntegrationType::IS_VISIBLE, '=', true);
    }
}
