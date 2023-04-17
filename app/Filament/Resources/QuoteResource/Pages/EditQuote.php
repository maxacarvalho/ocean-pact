<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Enums\QuoteStatusEnum;
use App\Events\QuoteRespondedEvent;
use App\Filament\Resources\QuoteResource;
use App\Models\Quote;
use App\Models\User;
use App\Utils\Str;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\DeleteAction as PageDeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getActions(): array
    {
        return [
            PageDeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSaveFormAction(): Action
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isSeller()) {
            return
                Action::make('send_quote')
                    ->label(Str::formatTitle(__('quote.form_save_action_label')))
                    ->action(fn () => $this->save())
                    ->requiresConfirmation()
                    ->modalSubheading(Str::ucfirst(__('quote.form_save_action_confirmation')));
        }

        return parent::getSaveFormAction();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isSeller()) {
            $data[Quote::STATUS] = QuoteStatusEnum::RESPONDED();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        /** @var User $user */
        $user = Auth::user();

        /** @var Quote $quote */
        $quote = $this->record;

        if ($user->isSeller() && $quote->isResponded()) {
            QuoteRespondedEvent::dispatch($quote->id);
        }
    }
}
