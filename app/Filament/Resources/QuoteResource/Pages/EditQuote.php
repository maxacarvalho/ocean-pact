<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Events\QuoteRespondedEvent;
use App\Filament\Resources\QuoteResource;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Utils\Str;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

/**
 * @property Quote $record
 */
class EditQuote extends EditRecord
{
    public bool $missingItemsUnitPriceOrDeliveryDate = false;

    protected static string $resource = QuoteResource::class;

    protected static string $view = 'filament::resources.pages.quote-custom-edit-record-page';

    public function sendQuote(): void
    {
        $items = $this->record->items->firstWhere(function (QuoteItem $item) {
            if (! $item->should_be_quoted) {
                return false;
            }

            return $item->should_be_quoted && ($item->unit_price <= 0 || $item->delivery_date === null);
        });

        if ($items) {
            $this->missingItemsUnitPriceOrDeliveryDate = true;

            return;
        }

        $this->record->markAsResponded();
        QuoteRespondedEvent::dispatch($this->record->id);

        $this->redirect(static::getResource()::getUrl());
    }

    public function cancel(): void
    {
        $this->redirect(static::getResource()::getUrl());
    }

    protected function getActions(): array
    {
        return [
            // PageDeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeSave(): void
    {
        $items = $this->record->items->firstWhere(function (QuoteItem $item) {
            if (! $item->should_be_quoted) {
                return false;
            }

            return $item->unit_price <= 0 || $item->delivery_date === null;
        });

        /** @var ViewField $incompleteItemsWarning */
        $incompleteItemsWarning = $this->form->getFlatFields()['incomplete_items_warning'];

        if ($items) {
            $incompleteItemsWarning->hidden(false);

            Notification::make()
                ->danger()
                ->title(Str::ucfirst(__('quote.quote_is_not_ready_to_be_sent')))
                ->body(Str::ucfirst(__('quote.please_fill_the_unit_price_and_delivery_date_for_all_items')))
                ->persistent()
                ->actions(fn () => null)
                ->send();

            $this->halt();
        }
    }
}
