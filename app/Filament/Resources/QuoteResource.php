<?php

namespace App\Filament\Resources;

use App\Enums\QuotesPortal\FreightTypeEnum;
use App\Enums\QuotesPortal\QuoteStatusEnum;
use App\Filament\Resources\QuoteResource\Pages\EditQuote;
use App\Filament\Resources\QuoteResource\Pages\ListQuotes;
use App\Filament\Resources\QuoteResource\Pages\QuoteAnalysisPanel;
use App\Filament\Resources\QuoteResource\Pages\ViewQuote;
use App\Filament\Resources\QuoteResource\RelationManagers\QuoteItemsRelationManager;
use App\Filament\Resources\QuoteResource\Widgets\QuotesOverviewWidget;
use App\Models\QuotesPortal\Budget;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Currency;
use App\Models\QuotesPortal\PaymentCondition;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use App\Models\QuotesPortal\Supplier;
use App\Models\QuotesPortal\SupplierUser;
use App\Models\Role;
use App\Models\User;
use App\Utils\Str;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

/**
 * @property Quote $record
 */
class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'far-receipt';

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('quote.quotes'));
    }

    public static function getModelLabel(): string
    {
        return Str::formatTitle(__('quote.quote'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('quote.quotes'));
    }

    public static function getWidgets(): array
    {
        return [
            QuotesOverviewWidget::class,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->columnSpan(function (Component $component) {
                        if (Auth::user()->hasAnyRole(Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN)) {
                            $component->columnSpan(['lg' => 2]);
                        }

                        $component->columnSpanFull();
                    })
                    ->schema([
                        Section::make()
                            ->columns(3)
                            ->schema([
                                Placeholder::make(Quote::PROPOSAL_NUMBER)
                                    ->label(Str::formatTitle(__('quote.proposal_number')))
                                    ->content(fn (Model|Quote $record) => $record->proposal_number),
                            ])
                            ->hiddenOn('create'),
                        Section::make()
                            ->columns(3)
                            ->schema([
                                Select::make(Quote::CURRENCY_ID)
                                    ->label(Str::formatTitle(__('quote.currency_id')))
                                    ->required()
                                    ->relationship(Quote::RELATION_CURRENCY, Currency::DESCRIPTION)
                                    ->live()
                                    ->afterStateUpdated(function (?int $state, ?int $old, Model|Quote $record) {
                                        if (! $state) {
                                            return;
                                        }

                                        /** @var Currency $currency */
                                        $currency = Currency::query()->findOrFail($state);
                                        $record->items()->update([
                                            QuoteItem::CURRENCY => $currency->iso_code,
                                        ]);
                                    }),

                                Select::make(Quote::PAYMENT_CONDITION_ID)
                                    ->label(Str::formatTitle(__('quote.payment_condition_id')))
                                    ->required()
                                    ->relationship(Quote::RELATION_PAYMENT_CONDITION, PaymentCondition::DESCRIPTION),

                                DatePicker::make(Quote::VALID_UNTIL)
                                    ->label(Str::formatTitle(__('quote.valid_until')))
                                    ->required()
                                    ->displayFormat('d/m/Y')
                                    ->hiddenOn('create'),
                            ]),

                        Section::make()
                            ->columns(3)
                            ->schema([
                                /*TextInput::make(Quote::EXPENSES)
                                    ->label(Str::formatTitle(__('quote.expenses')))
                                    ->default(0)
                                    ->mask(RawJs::make(<<<'JS'
                                      $money($input, ',', '.', 2)
                                    JS)),*/

                                Select::make(Quote::FREIGHT_TYPE)
                                    ->label(Str::formatTitle(__('quote.freight_type')))
                                    ->options(FreightTypeEnum::class),

                                /*TextInput::make(Quote::FREIGHT_COST)
                                    ->label(Str::formatTitle(__('quote.freight_cost')))
                                    ->default(0)
                                    ->mask(RawJs::make(<<<'JS'
                                      $money($input, ',', '.', 2)
                                    JS)),*/
                            ]),

                        Section::make()
                            ->columns(1)
                            ->schema([
                                Textarea::make(Quote::COMMENTS)
                                    ->label(Str::formatTitle(__('quote.comments')))
                                    ->columnSpanFull()
                                    ->live()
                                    ->afterStateUpdated(function (Model|Quote|null $record, $state) {
                                        $record->comments = $state;
                                        $record->save();
                                    })
                                    ->disabled(Auth::user()->isBuyer()),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->visible(fn () => Auth::user()->hasAnyRole(Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN))
                    ->schema([
                        Placeholder::make(Quote::PAYMENT_CONDITION_ID)
                            ->label(Str::formatTitle(__('quote.company_code')))
                            ->content(fn (Model|Quote $record) => $record->company->business_name),

                        Placeholder::make(Quote::COMPANY_ID)
                            ->label(Str::formatTitle(__('quote.company_code_branch')))
                            ->content(fn (Model|Quote $record) => $record->company->branch),

                        Placeholder::make(Quote::SUPPLIER_ID)
                            ->label(Str::formatTitle(__('quote.supplier_id')))
                            ->content(fn (Model|Quote $record) => $record->supplier->name),

                        Placeholder::make(Quote::BUYER_ID)
                            ->label(Str::formatTitle(__('quote.buyer_id')))
                            ->content(fn (Model|Quote $record) => $record->buyer->name),

                        Placeholder::make(Quote::BUDGET_ID)
                            ->label(Str::formatTitle(__('quote.budget_id')))
                            ->content(fn (Model|Quote $record) => $record->budget->budget_number),

                        Placeholder::make(Quote::QUOTE_NUMBER)
                            ->label(Str::formatTitle(__('quote.quote_number')))
                            ->content(fn (Model|Quote $record) => $record->quote_number),

                        Select::make(Quote::STATUS)
                            ->label(Str::formatTitle(__('quote.status')))
                            ->required()
                            ->options(QuoteStatusEnum::class),
                    ]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->striped()
            ->defaultSort(Quote::UPDATED_AT, 'desc')
            ->recordClasses(fn (Model|Quote $record) => match ($record->status) {
                QuoteStatusEnum::REPLACED => 'opacity-30',
                default => null,
            })
            ->modifyQueryUsing(function (EloquentBuilder $query) {
                /** @var User $user */
                $user = Auth::user();

                return $query
                    ->with([
                        Quote::RELATION_SUPPLIER => [
                            Supplier::RELATION_SELLERS,
                        ],
                        Quote::RELATION_BUDGET,
                    ])
                    ->where(Quote::STATUS, '!=', QuoteStatusEnum::DRAFT)
                    ->addSelect([
                        'company_name' => Company::query()->select(Company::BUSINESS_NAME)
                            ->from(Company::TABLE_NAME)
                            ->whereColumn(Company::ID, '=', Quote::COMPANY_ID)
                            ->limit(1),
                        'company_branch' => Company::query()->select(Company::BRANCH)
                            ->from(Company::TABLE_NAME)
                            ->whereColumn(Company::ID, '=', Quote::COMPANY_ID)
                            ->limit(1),
                    ])
                    ->when($user->isSeller(), function (EloquentBuilder $query) use ($user) {
                        $query->whereHas(
                            Quote::RELATION_SUPPLIER.'.'.Supplier::RELATION_SELLERS,
                            function (EloquentBuilder $query) use ($user) {
                                $query->where(SupplierUser::USER_ID, '=', $user->id);
                            }
                        );
                    })
                    ->when($user->isBuyer(), function (EloquentBuilder $query) use ($user) {
                        $query->where(Quote::BUYER_ID, '=', $user->id);
                    });
            })
            ->columns(
                self::getColumns()
            )
            ->filters(self::getFilters(), layout: FiltersLayout::Modal)
            ->actions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make()->exports([
                    ExcelExport::make()->fromTable()
                        ->withFilename(fn ($resource) => Str::slug($resource::getPluralModelLabel()).'-'.now()->format('Y-m-d'))
                        ->withColumns([
                            Column::make(Quote::STATUS)
                                ->formatStateUsing(fn (QuoteStatusEnum $state) => $state->getLabel()),
                        ]),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            QuoteItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuotes::route('/'),
            'view' => ViewQuote::route('/{record}'),
            // 'create' => CreateQuote::route('/create'),
            'edit' => EditQuote::route('/{record}/edit'),
            'quote-analysis-panel' => QuoteAnalysisPanel::route('/{companyId}/{quoteNumber}/quote-analysis-panel'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return Str::formatTitle(__('navigation.quotes'));
    }

    private static function getColumns(): array
    {
        return [
            TextColumn::make(Quote::PROPOSAL_NUMBER)
                ->label(Str::formatTitle(__('quote.proposal_number'))),

            TextColumn::make(Quote::RELATION_COMPANY.'.'.Company::NAME_AND_BRANCH)
                ->label(Str::formatTitle(__('quote.company'))),

            TextColumn::make(Quote::RELATION_BUDGET.'.'.Budget::BUDGET_NUMBER)
                ->label(Str::formatTitle(__('quote.budget_number')))
                ->sortable()
                ->searchable()
                ->visible(fn () => Auth::user()->isSuperAdmin() || Auth::user()->isAdmin() || Auth::user()->isSeller()),

            TextColumn::make(Quote::RELATION_SUPPLIER.'.'.Supplier::NAME)
                ->label(Str::formatTitle(__('quote.supplier')))
                ->sortable()
                ->searchable(),

            TextColumn::make(Quote::QUOTE_NUMBER)
                ->label(Str::formatTitle(__('quote.quote_number')))
                ->icon(function () {
                    if (! Auth::user()->isBuyer() && ! Auth::user()->isAdmin() && ! Auth::user()->isSuperAdmin()) {
                        return false;
                    }

                    return 'far-chart-user';
                })
                ->iconColor('primary')
                ->sortable()
                ->searchable()
                ->url(function (Quote $record) {
                    if (! Auth::user()->isBuyer() && ! Auth::user()->isAdmin() && ! Auth::user()->isSuperAdmin()) {
                        return false;
                    }

                    return self::getUrl('quote-analysis-panel', [
                        'companyId' => $record->company_id,
                        'quoteNumber' => $record->quote_number,
                    ]);
                }),

            TextColumn::make(Quote::STATUS)
                ->label(Str::formatTitle(__('quote.status')))
                ->sortable(),

            TextColumn::make(Quote::CREATED_AT)
                ->label(Str::formatTitle(__('quote.created_at')))
                ->dateTime('d/m/Y')
                ->sortable(),

            TextColumn::make(Quote::UPDATED_AT)
                ->label(Str::formatTitle(__('quote.updated_at')))
                ->dateTime('d/m/Y')
                ->sortable(),
        ];
    }

    private static function getFilters(): array
    {
        return [
            Filter::make('replaced_only')
                ->label(Str::formatTitle(__('quote.replaced_only')))
                ->query(fn (Builder $query): Builder => $query
                    ->whereNotNull(Quote::REPLACED_BY)
                    ->where(Quote::STATUS, QuoteStatusEnum::REPLACED)),

            Filter::make('latest_only')
                ->label(Str::formatTitle(__('quote.latest_only')))
                ->query(fn (Builder $query): Builder => $query
                    ->whereNull(Quote::REPLACED_BY)
                    ->where(Quote::STATUS, '!=', QuoteStatusEnum::REPLACED))
                ->default(),

            SelectFilter::make(Quote::COMPANY_ID)
                ->label(Str::formatTitle(__('quote.company')))
                ->relationship(
                    name: Quote::RELATION_COMPANY,
                    titleAttribute: Company::NAME_AND_BRANCH,
                    modifyQueryUsing: function (Builder $query): Builder {
                        return $query
                            ->when(
                                Auth::user()->isSeller(),
                                fn (Builder $query): Builder => $query->whereIn(
                                    Company::ID,
                                    Auth::user()->getSellerCompanies()->pluck(Company::ID)
                                )
                            );
                    }
                ),

            SelectFilter::make(Quote::STATUS)
                ->label(Str::formatTitle(__('quote.status')))
                ->options(QuoteStatusEnum::class),

            SelectFilter::make(Quote::SUPPLIER_ID)
                ->label(Str::formatTitle(__('quote.supplier')))
                ->relationship(
                    name: Quote::RELATION_SUPPLIER,
                    titleAttribute: Supplier::NAME,
                    modifyQueryUsing: function (Builder $query): Builder {
                        return $query
                            ->when(
                                Auth::user()->isSeller(),
                                fn (Builder $query): Builder => $query->whereHas(
                                    Supplier::RELATION_SELLERS,
                                    fn (Builder $query): Builder => $query->where(
                                        SupplierUser::USER_ID,
                                        Auth::user()->id
                                    )
                                )
                            );
                    }
                ),

            SelectFilter::make(Quote::BUDGET_ID)
                ->label(Str::formatTitle(__('quote.buyer')))
                ->relationship(Quote::RELATION_BUYER, User::NAME),

            Filter::make('created_at')
                ->label(Str::formatTitle(__('quote.created_at')))
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['created_from'] ?? null) {
                        $indicators['created_from'] = Str::ucfirst(__('quote.created_from', [
                            'date' => Carbon::parse($data['created_from'])->format('d/m/Y'),
                        ]));
                    }

                    if ($data['created_until'] ?? null) {
                        $indicators[''] = Str::ucfirst(__('quote.created_until', [
                            'date' => Carbon::parse($data['created_until'])->format('d/m/Y'),
                        ]));
                    }

                    return $indicators;
                })
                ->form([
                    DatePicker::make('created_from')
                        ->label(Str::formatTitle(__('quote.created_at')))
                        ->displayFormat('d/m/Y'),
                    DatePicker::make('created_until')
                        ->label(Str::formatTitle(__('quote.created_at')))
                        ->displayFormat('d/m/Y'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn (Builder $query, string $date): Builder => $query
                                ->whereDate(Quote::CREATED_AT, '>=', $date)
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, string $date): Builder => $query
                                ->whereDate(Quote::CREATED_AT, '<=', $date)
                        );
                }),
        ];
    }
}
