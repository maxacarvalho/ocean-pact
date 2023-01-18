<?php

namespace App\Filament\Resources\IntegrationTypeResource\Pages;

use App\Filament\Resources\IntegrationTypeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIntegrationTypes extends ListRecords
{
    protected static string $resource = IntegrationTypeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
