<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Utils\Str;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\DeleteAction as PageDeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getActions(): array
    {
        return [
            PageDeleteAction::make(),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return
            Action::make('send_quote')
                ->label(Str::formatTitle(__('quote.form_save_action_label')))
                ->action(fn () => $this->save())
                ->requiresConfirmation()
                ->modalSubheading(Str::ucfirst(__('quote.form_save_action_confirmation')));
    }
}
