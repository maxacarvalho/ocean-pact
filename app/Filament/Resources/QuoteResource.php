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
use App\Models\PaymentCondition;
use App\Models\Quote;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use App\Utils\Money;
use App\Utils\Str;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
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

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

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
                Select::make(Quote::COMPANY_CODE)
                    ->label(Str::formatTitle(__('product.company_code')))
                    ->relationship(Quote::RELATION_COMPANY, Company::NAME)
                    ->reactive()
                    ->visible(fn () => Auth::user()->hasAnyRole(Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN))
                    ->afterStateUpdated(function (Model|Quote|null $record, $state) {
                        $record->company_code = $state;
                        $record->save();
                    }),

                Select::make(Quote::COMPANY_CODE_BRANCH)
                    ->label(Str::formatTitle(__('product.company_code_branch')))
                    ->options(function (Closure $get, Model $record) {
                        $companyCode = $get(Quote::COMPANY_CODE);

                        if (null === $companyCode) {
                            return [];
                        }

                        return Company::query()
                            ->where(Company::CODE, '=', $companyCode)
                            ->pluck(Company::BRANCH, Company::CODE_BRANCH)
                            ->toArray();
                    })
                    ->visible(fn () => Auth::user()->hasAnyRole(Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN))
                    ->reactive()
                    ->afterStateUpdated(function (Model|Quote|null $record, $state) {
                        $record->company_code_branch = $state;
                        $record->save();
                    }),

                Select::make(Quote::SUPPLIER_ID)
                    ->label(Str::formatTitle(__('quote.supplier_id')))
                    ->required()
                    ->relationship(Quote::RELATION_SUPPLIER, Supplier::NAME)
                    ->visible(fn () => Auth::user()->hasAnyRole(Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN))
                    ->reactive()
                    ->afterStateUpdated(function (Model|Quote|null $record, $state) {
                        $record->supplier_id = $state;
                        $record->save();
                    }),

                Select::make(Quote::PAYMENT_CONDITION_ID)
                    ->label(Str::formatTitle(__('quote.payment_condition_id')))
                    ->required()
                    ->options(function (Closure $get) {
                        $companyCode = $get(Quote::COMPANY_CODE);

                        return PaymentCondition::query()
                            ->where(PaymentCondition::COMPANY_CODE, '=', $companyCode)
                            ->pluck(PaymentCondition::DESCRIPTION, PaymentCondition::ID)
                            ->toArray();
                    })
                    ->reactive()
                    ->afterStateUpdated(function (Model|Quote|null $record, $state) {
                        $record->payment_condition_id = $state;
                        $record->save();
                    }),

                Select::make(Quote::BUYER_ID)
                    ->label(Str::formatTitle(__('quote.buyer_id')))
                    ->required()
                    ->relationship(
                        Quote::RELATION_BUYER,
                        User::NAME,
                        function (Builder $query) {
                            $query->whereHas(User::RELATION_ROLES, function (Builder $query) {
                                $query->where(Role::NAME, Role::ROLE_BUYER);
                            });
                        }
                    )
                    ->visible(fn () => Auth::user()->hasAnyRole(Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN)),

                Select::make(Quote::BUDGET_ID)
                    ->label(Str::formatTitle(__('quote.budget_id')))
                    ->required()
                    ->relationship(
                        Quote::RELATION_BUDGET,
                        Budget::BUDGET_NUMBER
                    )
                    ->visible(fn () => Auth::user()->hasAnyRole(Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN)),

                TextInput::make(Quote::QUOTE_NUMBER)
                    ->label(Str::formatTitle(__('quote.quote_number')))
                    ->required()
                    ->visible(fn () => Auth::user()->hasAnyRole(Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN)),

                Select::make(Quote::STATUS)
                    ->label(Str::formatTitle(__('quote.status')))
                    ->required()
                    ->options(fn () => QuoteStatusEnum::toArray())
                    ->visible(fn () => Auth::user()->hasAnyRole(Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN)),

                DatePicker::make(Quote::VALID_UNTIL)
                    ->label(Str::formatTitle(__('quote.valid_until')))
                    ->required()
                    ->displayFormat('d/m/Y')
                    ->hiddenOn('create')
                    ->reactive()
                    ->afterStateUpdated(function (Model|Quote|null $record, $state) {
                        $record->valid_until = $state;
                        $record->save();
                    }),

                Grid::make(3)
                    ->schema([
                        TextInput::make(Quote::IPI)
                            ->label(Str::formatTitle(__('quote.ipi')))
                            ->default(0)
                            ->mask(fn (TextInput\Mask $mask) => $mask
                                ->patternBlocks([
                                    'percentage' => fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->mapToDecimalSeparator([','])
                                        ->signed(true)
                                        ->normalizeZeros()
                                        ->padFractionalZeros()
                                        ->thousandsSeparator('.'),
                                ])
                                ->pattern('percentage')
                                ->lazyPlaceholder(false)
                            )
                            ->reactive()
                            ->afterStateUpdated(function (Model|Quote|null $record, $state) {
                                if ($state) {
                                    $record->ipi = Money::fromMonetary($state)->toMinor();
                                    $record->save();
                                }
                            }),

                        TextInput::make(Quote::ICMS)
                            ->label(Str::formatTitle(__('quote.icms')))
                            ->default(0)
                            ->mask(fn (TextInput\Mask $mask) => $mask
                                ->patternBlocks([
                                    'percentage' => fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->mapToDecimalSeparator([','])
                                        ->signed(true)
                                        ->normalizeZeros()
                                        ->padFractionalZeros()
                                        ->thousandsSeparator('.'),
                                ])
                                ->pattern('percentage')
                                ->lazyPlaceholder(false)
                            )
                            ->reactive()
                            ->afterStateUpdated(function (Model|Quote|null $record, $state) {
                                if ($state) {
                                    $record->icms = Money::fromMonetary($state)->toMinor();
                                    $record->save();
                                }
                            }),

                        TextInput::make(Quote::EXPENSES)
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
                                ->pattern('R$money')
                                ->lazyPlaceholder(false)
                            )
                            ->reactive()
                            ->afterStateUpdated(function (Model|Quote|null $record, $state) {
                                if ($state) {
                                    $record->expenses = Money::fromMonetary($state)->toMinor();
                                    $record->save();
                                }
                            }),

                        Select::make(Quote::FREIGHT_TYPE)
                            ->label(Str::formatTitle(__('quote.freight_type')))
                            ->options(fn () => FreightTypeEnum::toArray())
                            ->reactive()
                            ->afterStateUpdated(function (Model|Quote|null $record, $state) {
                                $record->freight_type = $state;
                                $record->save();
                            }),

                        TextInput::make(Quote::FREIGHT_COST)
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
                                ->pattern('R$money')
                                ->lazyPlaceholder(false)
                            )
                            ->reactive()
                            ->afterStateUpdated(function (Model|Quote|null $record, $state) {
                                if ($state) {
                                    $record->freight_cost = Money::fromMonetary($state)->toMinor();
                                    $record->save();
                                }
                            }),
                    ])
                    ->columnSpanFull(),

                Textarea::make(Quote::COMMENTS)
                    ->label(Str::formatTitle(__('quote.comments')))
                    ->columnSpanFull()
                    ->reactive()
                    ->afterStateUpdated(function (Model|Quote|null $record, $state) {
                        $record->comments = $state;
                        $record->save();
                    }),
            ]);
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
                    ->options(fn () => QuoteStatusEnum::toArray()),

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
