<?php

namespace App\Filament\Resources\IntegrationTypeResource\Pages;

use App\Filament\Resources\IntegrationTypeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIntegrationType extends EditRecord
{
    protected static string $resource = IntegrationTypeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
