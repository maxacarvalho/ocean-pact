<?php

namespace App\Livewire\QuoteAnalysisPanel;

use App\Actions\QuotesPortal\CreateQuoteContactRequestAction;
use App\Actions\QuotesPortal\RequestNewProposalAction;
use App\Enums\QuotesPortal\QuoteStatusEnum;
use App\Models\QuotesPortal\PredictedPurchaseRequest as PredictedPurchaseRequestModel;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use App\Utils\Str;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * @property-read Form $contactRequestForm
 */
class SupplierQuote extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Quote $quote;
    #[Locked]
    public int $quoteId;
    public QuoteStatusEnum $quoteStatus;
    public bool $isQuoteBuyerOwner;
    public string $supplierName;
    public ?array $contactRequestFormData = [];
    public ?array $predictedPurchaseRequestSelectedQuoteItems = [];

    public function mount(int $quoteId, bool $isQuoteBuyerOwner): void
    {
        $this->quoteId = $quoteId;
        $this->isQuoteBuyerOwner = $isQuoteBuyerOwner;

        $this->quote = $this->getQuote($quoteId);

        $this->quoteStatus = $this->quote->status;
        $this->supplierName = $this->quote->supplier->name;

        $this->predictedPurchaseRequestSelectedQuoteItems = PredictedPurchaseRequestModel::query()
            ->where(PredictedPurchaseRequestModel::COMPANY_ID, $this->quote->company_id)
            ->where(PredictedPurchaseRequestModel::QUOTE_NUMBER, $this->quote->quote_number)
            ->pluck('quote_item_id')
            ->toArray();
    }

    public function render(): View|Application|Factory
    {
        return view('livewire.quote-analysis-panel.supplier-quote');
    }

    #[On('predictedPurchaseRequestLoaded')]
    public function onPredictedPurchaseRequestLoaded($selectedQuoteItems): void
    {
        $this->predictedPurchaseRequestSelectedQuoteItems = $selectedQuoteItems;
    }

    public function table(Table $table): Table
    {
        return $table
            ->header(
                view('livewire.quote-analysis-panel.supplier-quote.supplier-quote-table-header', [
                    'supplierName' => $this->supplierName,
                    'proposal' => $this->quote->proposal_number,
                    'statusColor' => match ($this->quoteStatus) {
                        QuoteStatusEnum::PENDING => 'warning',
                        QuoteStatusEnum::RESPONDED => 'success',
                        default => 'gray',
                    },
                    'statusLabel' => $this->quoteStatus->getLabel(),
                ])
            )
            ->query(fn (): Builder => QuoteItem::query()->where(QuoteItem::QUOTE_ID, $this->quoteId))
            ->queryStringIdentifier("supplier-quote-{$this->quoteId}")
            ->defaultSort(QuoteItem::ITEM)
            ->columns([
                TextColumn::make(QuoteItem::ITEM)
                    ->label(Str::title(__('quote_item.item'))),

                TextColumn::make(QuoteItem::DESCRIPTION)
                    ->label(Str::title(__('quote_item.description')))
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    }),

                TextColumn::make(QuoteItem::UNIT_PRICE)
                    ->label(Str::title(__('quote_item.unit_price'))),

                TextColumn::make(QuoteItem::DELIVERY_IN_DAYS)
                    ->label(__('quote_analysis_panel.eta'))
                    ->formatStateUsing(function (int $state): string {
                        return Carbon::now()->addDays($state)->format('d/m/Y');
                    }),

                CheckboxColumn::make('is_selected')
                    ->label(Str::ucfirst(__('quote_analysis_panel.is_selected')))
                    ->state(function (QuoteItem $quoteItem) {
                        return in_array($quoteItem->id, $this->predictedPurchaseRequestSelectedQuoteItems, true);
                    })
                    ->updateStateUsing(function ($state, QuoteItem $quoteItem) {
                        if (false === $state) {
                            $this->predictedPurchaseRequestSelectedQuoteItems = array_diff($this->predictedPurchaseRequestSelectedQuoteItems, [$quoteItem->id]);
                        } else {
                            $this->predictedPurchaseRequestSelectedQuoteItems[] = $quoteItem->id;
                        }

                        $this->dispatch('predictedPurchaseRequestItemToggled',
                            quoteItemId: $quoteItem->id,
                            item: $quoteItem->item,
                            productId: $quoteItem->product_id,
                            state: $state
                        );

                        return $state;
                    }),
            ]);
    }

    public function canRequestNewProposal(): bool
    {
        return $this->quoteStatus->equals(QuoteStatusEnum::RESPONDED);
    }

    public function requestNewProposalConfirmationModal(): void
    {
        $this->dispatch('open-modal', id: "request-new-proposal-confirmation-modal-{$this->quoteId}");
    }

    public function requestNewProposal(RequestNewProposalAction $requestNewProposalAction): void
    {
        $requestNewProposalAction->handle($this->quoteId);

        $this->dispatch('newQuoteProposalRequested');

        $this->dispatch('close-modal', id: "request-new-proposal-confirmation-modal-{$this->quoteId}");
    }

    protected function getForms(): array
    {
        return [
            'contactRequestForm',
        ];
    }

    public function contactRequestForm(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('body')
                    ->label(Str::ucfirst(__('quote_analysis_panel.request_contact_form_body_label')))
                    ->required()
                    ->rows(5)
                    ->autosize(),
            ])
            ->statePath('contactRequestFormData');
    }

    public function openRequestContactModal(): void
    {
        $this->dispatch('open-modal', id: "request-contact-modal-{$this->quoteId}");
    }

    public function requestContact(CreateQuoteContactRequestAction $createQuoteContactRequestAction): void
    {
        /** @var array{body: string} $data */
        $data = $this->contactRequestForm->getState();

        $createQuoteContactRequestAction->handle($this->quoteId, auth()->id(), $data['body']);

        $this->contactRequestForm->fill();

        Notification::make()
            ->title(Str::ucfirst(__('quote_analysis_panel.request_contact_sent_successfully')))
            ->success()
            ->send();

        $this->dispatch('close-modal', id: "request-contact-modal-{$this->quoteId}");
    }

    private function getQuote(int $quoteId): Quote
    {
        /** @var Quote $quote */
        $quote = Quote::query()
            ->with([
                Quote::RELATION_SUPPLIER,
            ])
            ->findOrFail($quoteId);

        return $quote;
    }
}
