<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Events\QuoteRespondedEvent;
use App\Filament\Resources\QuoteResource;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Utils\Money;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\RoundingMode;
use Filament\Resources\Pages\EditRecord;
use Throwable;

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
    }

    public function cancel(): void
    {
        $this->redirect(static::getResource()::getUrl());
    }

    public function getTotal(): string
    {
        try {
            $this->record->load(Quote::RELATION_ITEMS);

            $sum = (int) $this->record->items->reduce(function (float $carry, QuoteItem $item) {
                return $carry + ($item->unit_price * $item->quantity);
            }, 0);
            $subtotal = Money::fromMinor($sum);
            $tax = Money::fromMinor($this->record->ipi);
            $taxAmount = $subtotal->getBrickMoney()->multipliedBy(
                $tax->getBrickMoney()->getAmount()->toFloat() / 100,
                RoundingMode::UP
            );
            $total = $subtotal->getBrickMoney()->plus($taxAmount);

            return $total->formatTo(config('app.locale'));
        } catch (Throwable $e) {
            return '0,00';
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        try {
            $data[Quote::EXPENSES] = Money::fromMonetary($data[Quote::EXPENSES])->toMinor();
            $data[Quote::FREIGHT_COST] = Money::fromMonetary($data[Quote::FREIGHT_COST])->toMinor();
        } catch (NumberFormatException $exception) {
            $data[Quote::EXPENSES] = Money::parse($data[Quote::EXPENSES])->toMinor();
            $data[Quote::FREIGHT_COST] = Money::parse($data[Quote::FREIGHT_COST])->toMinor();
        }

        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data[Quote::EXPENSES] = Money::fromMinor($data[Quote::EXPENSES])->toDecimal();
        $data[Quote::FREIGHT_COST] = Money::fromMinor($data[Quote::FREIGHT_COST])->toDecimal();

        return $data;
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
}
