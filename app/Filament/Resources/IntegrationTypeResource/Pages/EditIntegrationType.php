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
        $savedData = $this->data;

        return [
            Action::make('execute')
                ->label(Str::formatTitle(__('integration_type.execute')))
                ->icon('heroicon-o-play')
                ->disabled(fn () => ! $this->record->isCallable() || $savedData !== $this->data)
                ->visible(fn () => $this->record->isCallable())
                ->action('executeIntegrationType'),
            PageDeleteAction::make(),
        ];
    }

    public function executeIntegrationType(): void
    {
        dispatch(new CallExternalApiIntegrationJob($this->record));
    }
}
