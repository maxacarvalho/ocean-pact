<?php

namespace App\Filament\Resources;

use App\Enums\FreightTypeEnum;
use App\Enums\QuoteStatusEnum;
use App\Filament\Resources\QuoteResource\Pages\EditQuote;
use App\Filament\Resources\QuoteResource\Pages\ListQuotes;
use App\Filament\Resources\QuoteResource\Pages\ViewQuote;
use App\Filament\Resources\QuoteResource\RelationManagers\QuoteItemsRelationManager;
use App\Models\Budget;
use App\Models\Company;
use App\Models\Currency;
use App\Models\PaymentCondition;
use App\Models\Quote;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use App\Utils\Str;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Actions\ViewAction as TableViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter as TableFilter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
                        Card::make()
                            ->columns(3)
                            ->schema([
                                Select::make(Quote::CURRENCY_ID)
                                    ->label(Str::formatTitle(__('quote.currency_id')))
                                    ->required()
                                    ->relationship(Quote::RELATION_CURRENCY, Currency::DESCRIPTION),

                                Select::make(Quote::PAYMENT_CONDITION_ID)
                                    ->label(Str::formatTitle(__('quote.payment_condition_id')))
                                    ->required()
                                    ->options(function (Closure $get) {
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

                        Card::make()
                            ->columns(3)
                            ->schema([
                                /*TextInput::make(Quote::EXPENSES)
                                    ->label(Str::formatTitle(__('quote.expenses')))
                                    ->default(0)
                                    ->mask(fn (TextInput\Mask $mask) => $mask
                                        ->patternBlocks([
                                            'money' => fn (Mask $mask) => $mask
                                                ->numeric()
                                                ->decimalPlaces(2)
                                                ->decimalSeparator(',')
                                                ->mapToDecimalSeparator([','])
                                                ->signed(true)
                                                ->normalizeZeros()
                                                ->padFractionalZeros()
                                                ->thousandsSeparator('.'),
                                        ])
                                        ->pattern('money')
                                        ->lazyPlaceholder(false)
                                    ),*/

                                Select::make(Quote::FREIGHT_TYPE)
                                    ->label(Str::formatTitle(__('quote.freight_type')))
                                    ->options(fn () => FreightTypeEnum::toArray()),

                                /*TextInput::make(Quote::FREIGHT_COST)
                                    ->label(Str::formatTitle(__('quote.freight_cost')))
                                    ->default(0)
                                    ->mask(fn (TextInput\Mask $mask) => $mask
                                        ->patternBlocks([
                                            'money' => fn (Mask $mask) => $mask
                                                ->numeric()
                                                ->decimalPlaces(2)
                                                ->decimalSeparator(',')
                                                ->mapToDecimalSeparator([','])
                                                ->signed(true)
                                                ->normalizeZeros()
                                                ->padFractionalZeros()
                                                ->thousandsSeparator('.'),
                                        ])
                                        ->pattern('money')
                                        ->lazyPlaceholder(false)
                                    ),*/
                            ]),

                        Card::make()
                            ->columns(1)
                            ->schema([
                                Textarea::make(Quote::COMMENTS)
                                    ->label(Str::formatTitle(__('quote.comments')))
                                    ->columnSpanFull()
                                    ->reactive()
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
                            ->options(fn () => collect(QuoteStatusEnum::toArray())->except('DRAFT')->toArray()),
                    ]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                    ->sortable()
                    ->formatStateUsing(fn (?string $state) => QuoteStatusEnum::from($state)->label),

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
                    ->options(fn () => collect(QuoteStatusEnum::toArray())->except('DRAFT')->toArray()),

                SelectFilter::make(Quote::SUPPLIER_ID)
                    ->label(Str::formatTitle(__('quote.supplier')))
                    ->relationship(Quote::RELATION_SUPPLIER, Supplier::NAME),

                SelectFilter::make(Quote::BUDGET_ID)
                    ->label(Str::formatTitle(__('quote.buyer')))
                    ->relationship(Quote::RELATION_BUYER, User::NAME),
            ])
            ->actions([
                TableEditAction::make(),
                TableViewAction::make(),
            ])
            ->bulkActions([
                //
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

    protected static function getNavigationGroup(): ?string
    {
        return Str::formatTitle(__('navigation.quotes'));
    }
}
