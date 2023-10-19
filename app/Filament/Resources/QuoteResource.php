<?php

namespace App\Filament\Resources;

use App\Enums\FreightTypeEnum;
use App\Enums\QuoteStatusEnum;
use App\Filament\Resources\QuoteResource\Pages\EditQuote;
use App\Filament\Resources\QuoteResource\Pages\ListQuotes;
use App\Filament\Resources\QuoteResource\Pages\ViewQuote;
use App\Filament\Resources\QuoteResource\RelationManagers\QuoteItemsRelationManager;
use App\Filament\Resources\QuoteResource\Widgets\QuotesOverviewWidget;
use App\Models\Budget;
use App\Models\Company;
use App\Models\Currency;
use App\Models\PaymentCondition;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Role;
use App\Models\Supplier;
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
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Actions\ViewAction as TableViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter as TableFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as DbQueryBuilder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
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
                                    ->options(function (\Filament\Forms\Get $get) {
                                        $companyCode = $get(Quote::COMPANY_CODE);

                                        return PaymentCondition::query()
                                            ->where(PaymentCondition::COMPANY_CODE, '=', $companyCode)
                                            ->pluck(PaymentCondition::DESCRIPTION, PaymentCondition::ID)
                                            ->toArray();
                                    }),

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
                                    }),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->visible(fn () => Auth::user()->hasAnyRole(Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN))
                    ->schema([
                        Placeholder::make(Quote::PAYMENT_CONDITION_ID)
                            ->label(Str::formatTitle(__('quote.company_code')))
                            ->content(fn (Model|Quote $record) => $record->company->business_name),

                        Placeholder::make(Quote::COMPANY_CODE_BRANCH)
                            ->label(Str::formatTitle(__('quote.company_code_branch')))
                            ->content(fn (Model|Quote $record) => $record->company()->where(Company::CODE_BRANCH, '=', $record->company_code_branch)->first()->branch),

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
            ->modifyQueryUsing(function (EloquentBuilder $query) {
                /** @var User $user */
                $user = Auth::user();

                return $query
                    ->with([Quote::RELATION_SUPPLIER, Quote::RELATION_BUDGET])
                    ->select([
                        Quote::TABLE_NAME.'.'.Quote::ID,
                        Quote::TABLE_NAME.'.'.Quote::COMPANY_CODE,
                        Quote::TABLE_NAME.'.'.Quote::COMPANY_CODE_BRANCH,
                        Quote::TABLE_NAME.'.'.Quote::BUDGET_ID,
                        Quote::TABLE_NAME.'.'.Quote::SUPPLIER_ID,
                        Quote::TABLE_NAME.'.'.Quote::PAYMENT_CONDITION_ID,
                        Quote::TABLE_NAME.'.'.Quote::BUYER_ID,
                        Quote::TABLE_NAME.'.'.Quote::QUOTE_NUMBER,
                        Quote::TABLE_NAME.'.'.Quote::VALID_UNTIL,
                        Quote::TABLE_NAME.'.'.Quote::STATUS,
                        Quote::TABLE_NAME.'.'.Quote::COMMENTS,
                        Quote::TABLE_NAME.'.'.Quote::CREATED_AT,
                        Quote::TABLE_NAME.'.'.Quote::UPDATED_AT,
                    ])
                    ->where(Quote::TABLE_NAME.'.'.Quote::STATUS, '!=', QuoteStatusEnum::DRAFT)
                    ->addSelect([
                        'company_name' => fn (DbQueryBuilder $query) => $query->select(Company::BUSINESS_NAME)
                            ->from(Company::TABLE_NAME)
                            ->whereColumn(
                                Company::TABLE_NAME.'.'.Company::CODE,
                                '=',
                                Quote::TABLE_NAME.'.'.Quote::COMPANY_CODE
                            )
                            ->limit(1),
                        'company_branch' => fn (DbQueryBuilder $query) => $query->select(Company::BRANCH)
                            ->from(Company::TABLE_NAME)
                            ->whereColumn(
                                Company::TABLE_NAME.'.'.Company::CODE,
                                '=',
                                Quote::TABLE_NAME.'.'.Quote::COMPANY_CODE
                            )
                            ->whereColumn(
                                Company::TABLE_NAME.'.'.Company::CODE_BRANCH,
                                '=',
                                Quote::TABLE_NAME.'.'.Quote::COMPANY_CODE_BRANCH
                            )
                            ->limit(1),
                    ])
                    ->when($user->isSeller(), function (EloquentBuilder $query) use ($user) {
                        $query->where(Quote::TABLE_NAME.'.'.Quote::SUPPLIER_ID, '=', $user->supplier_id);
                    });
            })
            ->columns([
                TextColumn::make('company_name')
                    ->label(Str::formatTitle(__('quote.company_code')))
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy(
                            Company::query()
                                ->select(Company::TABLE_NAME.'.'.Company::BUSINESS_NAME)
                                ->whereColumn(
                                    Company::TABLE_NAME.'.'.Company::CODE,
                                    '=',
                                    Quote::TABLE_NAME.'.'.Quote::COMPANY_CODE
                                )
                                ->limit(1),
                            $direction
                        );
                    }),

                TextColumn::make('company_branch')
                    ->label(Str::formatTitle(__('quote.company_code_branch')))
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy(
                            Company::query()
                                ->select(Company::TABLE_NAME.'.'.Company::BRANCH)
                                ->whereColumn(
                                    Company::TABLE_NAME.'.'.Company::CODE,
                                    '=',
                                    Quote::TABLE_NAME.'.'.Quote::COMPANY_CODE
                                )
                                ->whereColumn(
                                    Company::TABLE_NAME.'.'.Company::CODE_BRANCH,
                                    '=',
                                    Quote::TABLE_NAME.'.'.Quote::COMPANY_CODE_BRANCH
                                )
                                ->limit(1),
                            $direction
                        );
                    }),

                TextColumn::make(Quote::RELATION_BUDGET.'.'.Budget::BUDGET_NUMBER)
                    ->label(Str::formatTitle(__('quote.budget_number')))
                    ->sortable()
                    ->searchable()
                    ->visible(fn () => Auth::user()->isSuperAdmin() || Auth::user()->isAdmin() || Auth::user()->isSeller()),

                TextColumn::make(Quote::RELATION_SUPPLIER.'.'.Supplier::NAME)
                    ->label(Str::formatTitle(__('quote.supplier')))
                    ->sortable()
                    ->searchable()
                    ->visible(fn () => Auth::user()->isSuperAdmin() || Auth::user()->isAdmin() || Auth::user()->isBuyer()),

                TextColumn::make(Quote::QUOTE_NUMBER)
                    ->label(Str::formatTitle(__('quote.quote_number')))
                    ->sortable()
                    ->searchable(),

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
            ])
            ->filters([
                TableFilter::make(Quote::COMPANY_CODE)
                    ->label(Str::formatTitle(__('quote.company_code')))
                    ->form([
                        Select::make(Quote::COMPANY_CODE)
                            ->label(Str::formatTitle(__('quote.company_code')))
                            ->options(fn () => Company::all()->sortBy(Company::NAME)->pluck(Company::NAME, Company::CODE)),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data[Quote::COMPANY_CODE],
                            fn (Builder $query, string $companyCode): Builder => $query->where(Quote::COMPANY_CODE, '=', $companyCode)
                        );
                    }),

                SelectFilter::make(Quote::STATUS)
                    ->label(Str::formatTitle(__('quote.status')))
                    ->options(QuoteStatusEnum::class),

                SelectFilter::make(Quote::SUPPLIER_ID)
                    ->label(Str::formatTitle(__('quote.supplier')))
                    ->relationship(Quote::RELATION_SUPPLIER, Supplier::NAME),

                SelectFilter::make(Quote::BUDGET_ID)
                    ->label(Str::formatTitle(__('quote.buyer')))
                    ->relationship(Quote::RELATION_BUYER, User::NAME),

                TableFilter::make('created_at')
                    ->label(Str::formatTitle(__('quote.created_at')))
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = Str::ucfirst(__('quote.created_from', ['date' => Carbon::parse($data['created_from'])->format('d/m/Y')]));
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[''] = Str::ucfirst(__('quote.created_until', ['date' => Carbon::parse($data['created_until'])->format('d/m/Y')]));
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
                                fn (Builder $query, string $date): Builder => $query->whereDate(Quote::CREATED_AT, '>=', $date)
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, string $date): Builder => $query->whereDate(Quote::CREATED_AT, '<=', $date)
                            );
                    }),
            ])
            ->actions([
                TableEditAction::make(),
                TableViewAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make()->exports([
                    ExcelExport::make()
                        ->withFilename(fn ($resource) => Str::slug($resource::getPluralModelLabel()).'-'.now()->format('Y-m-d')),
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
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return Str::formatTitle(__('navigation.quotes'));
    }
}
