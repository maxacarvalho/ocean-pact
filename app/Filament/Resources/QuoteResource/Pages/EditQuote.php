<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Events\QuoteRespondedEvent;
use App\Filament\Resources\QuoteResource;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use App\Utils\Str;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

/**
 * @property Quote $record
 */
class EditQuote extends EditRecord
{
    public bool $missingItemsUnitPriceOrDeliveryDate = false;

    protected static string $resource = QuoteResource::class;

    protected static string $view = 'filament-panels::resources.pages.quote-custom-edit-record-page';

    protected function afterValidate(): void
    {
        if (Auth::user()->isSeller()) {
            $items = $this->record->items->firstWhere(function (QuoteItem $item) {
                if (! $item->should_be_quoted) {
                    return false;
                }

                return $item->should_be_quoted && ($item->unit_price <= 0 || $item->delivery_in_days === 0);
            });

            if ($items) {
                $this->missingItemsUnitPriceOrDeliveryDate = true;

                Notification::make()
                    ->danger()
                    ->title(Str::ucfirst(__('quote.please_fill_the_unit_price_and_delivery_in_days_for_all_items')))
                    ->icon('far-circle-exclamation')
                    ->color('danger')
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }
    }

    protected function afterSave(): void
    {
        if (Auth::user()->isSeller()) {
            $this->record->markAsResponded();

            QuoteRespondedEvent::dispatch($this->record->id);
        }
    }

    public function sendQuote(): Action
    {
        return Action::make('sendQuote')
            ->label(Str::formatTitle(__('quote.form_save_action_label')))
            ->action('save');
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
