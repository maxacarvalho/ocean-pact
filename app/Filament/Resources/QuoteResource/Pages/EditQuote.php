<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Events\QuoteRespondedEvent;
use App\Filament\Resources\QuoteResource;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Utils\Str;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

/**
 * @property Quote $record
 */
class EditQuote extends EditRecord
{
    public bool $missingItemsUnitPriceOrDeliveryDate = false;

    protected static string $resource = QuoteResource::class;

    protected static string $view = 'filament-panels::resources.pages.quote-custom-edit-record-page';

    public function sendQuote(): Action
    {
        return Action::make('sendQuote')
            ->label(Str::formatTitle(__('quote.form_save_action_label')))
            ->requiresConfirmation()
            ->modalDescription(__('quote.form_save_action_confirmation'))
            ->action(function () {
                $this->validate();
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

                $this->save(false);

                $this->record->markAsResponded();
                QuoteRespondedEvent::dispatch($this->record->id);

                $this->redirect(static::getResource()::getUrl());
            });
    }

    public function cancelEditQuote(): Action
    {
        return Action::make('cancel')
            ->label(Str::ucfirst(__('quote.cancel_edit_quote_label')))
            ->url(QuoteResource::getUrl())
            ->color('gray');
    }

    /*protected function mutateFormDataBeforeSave(array $data): array
    {
        try {
            $data[Quote::EXPENSES] = Money::fromMonetary($data[Quote::EXPENSES])->toMinor();
            $data[Quote::FREIGHT_COST] = Money::fromMonetary($data[Quote::FREIGHT_COST])->toMinor();
        } catch (NumberFormatException $exception) {
            $data[Quote::EXPENSES] = Money::parse($data[Quote::EXPENSES])->toMinor();
            $data[Quote::FREIGHT_COST] = Money::parse($data[Quote::FREIGHT_COST])->toMinor();
        }

        return $data;
    }*/

    /*protected function mutateFormDataBeforeFill(array $data): array
    {
        $data[Quote::EXPENSES] = Money::fromMinor($data[Quote::EXPENSES])->toDecimal();
        $data[Quote::FREIGHT_COST] = Money::fromMinor($data[Quote::FREIGHT_COST])->toDecimal();

        return $data;
    }*/

    protected function getHeaderActions(): array
    {
        return [
            // PageDeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
