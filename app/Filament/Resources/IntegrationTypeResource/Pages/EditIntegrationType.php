<?php

namespace App\Filament\Resources\IntegrationTypeResource\Pages;

use App\Filament\Resources\IntegrationTypeResource;
use App\Jobs\IntegraHub\CallExternalApiIntegrationJob;
use App\Utils\Str;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction as PageDeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIntegrationType extends EditRecord
{
    protected static string $resource = IntegrationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('execute')
                ->label(Str::formatTitle(__('integration_type.execute')))
                ->icon('heroicon-o-play')
                ->visible($this->record->isCallable())
                ->action('executeIntegrationType'),
            PageDeleteAction::make(),
        ];
    }

    public function executeIntegrationType(): void
    {
        dispatch(new CallExternalApiIntegrationJob($this->record));
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
