<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Currency;
use App\Models\QuotesPortal\Product;
use App\Utils\Str;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables\Actions\DeleteBulkAction as TableDeleteBulkAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter as TableFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'far-box-open';

    public static function getNavigationLabel(): string
    {
        return Str::title(__('product.products'));
    }

    public static function getModelLabel(): string
    {
        return Str::title(__('product.product'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::title(__('product.products'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make(Product::COMPANY_CODE)
                    ->label(Str::title(__('product.company_code')))
                    ->relationship(Product::RELATION_COMPANY, Company::NAME)
                    ->reactive(),

                Select::make(Product::COMPANY_CODE_BRANCH)
                    ->label(Str::title(__('product.company_code_branch')))
                    ->options(function (Get $get) {
                        $companyCode = $get(Product::COMPANY_CODE);

                        if (null === $companyCode) {
                            return [];
                        }

                        return Company::query()
                            ->where(Company::CODE, '=', $companyCode)
                            ->pluck(Company::BRANCH, Company::CODE_BRANCH)
                            ->toArray();
                    }),

                TextInput::make(Product::CODE)
                    ->label(Str::title(__('product.code')))
                    ->required(),

                TextInput::make(Product::DESCRIPTION)
                    ->label(Str::title(__('product.description')))
                    ->required(),

                TextInput::make(Product::MEASUREMENT_UNIT)
                    ->label(Str::title(__('product.measurement_unit')))
                    ->required(),

                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        Fieldset::make(Str::title(__('product.last_price')))
                            ->columnSpan(1)
                            ->schema([
                                Select::make('last_price_currency')
                                    ->label(Str::title(__('product.last_price_currency')))
                                    ->options(fn () => Currency::query()->distinct()->pluck(Currency::ISO_CODE, Currency::ISO_CODE)->toArray())
                                    ->required()
                                    ->default('BRL'),

                                TextInput::make(Product::LAST_PRICE)
                                    ->label(Str::title(__('product.last_price')))
                                    ->required()
                                    ->formatStateUsing(function ($state, Model|Product|null $record, Get $get) {
                                        $currency = null === $record ? $get('last_price_currency') : $record->last_price->currency;

                                        if (null === $record) {
                                            return 'BRL' === $currency ? '0,00' : '0.00';
                                        }

                                        return $record->last_price->getFormattedAmount();

                                    })
                                    ->mask(function (TextInput $component, Model|Product|null $record, Get $get) {
                                        $currency = null === $record ? $get('last_price_currency') : $record->last_price->currency;

                                        if ('BRL' === $currency) {
                                            return RawJs::make('$money($input, \',\', \'.\')');
                                        }

                                        return RawJs::make('$money($input)');
                                    }),
                            ]),

                        Fieldset::make(Str::title(__('product.smallest_price')))
                            ->columnSpan(1)
                            ->schema([
                                Select::make('smallest_price_currency')
                                    ->label(Str::title(__('product.smallest_price_currency')))
                                    ->options(fn () => Currency::query()->distinct()->pluck(Currency::ISO_CODE, Currency::ISO_CODE)->toArray())
                                    ->required()
                                    ->default('BRL'),

                                TextInput::make(Product::SMALLEST_PRICE)
                                    ->label(Str::title(__('product.smallest_price')))
                                    ->required()
                                    ->formatStateUsing(function ($state, Model|Product|null $record, Get $get) {
                                        $currency = null === $record ? $get('smallest_price_currency') : $record->last_price->currency;

                                        if (null === $record) {
                                            return 'BRL' === $currency ? '0,00' : '0.00';
                                        }

                                        return $record->smallest_price->getFormattedAmount();
                                    })
                                    ->mask(function (Model|Product|null $record, Get $get) {
                                        $currency = null === $record ? $get('smallest_price_currency') : $record->last_price->currency;

                                        if ('BRL' === $currency) {
                                            return RawJs::make('$money($input, \',\', \'.\')');
                                        }

                                        return RawJs::make('$money($input)');
                                    }),
                            ]),

                        TextInput::make(Product::SMALLEST_ETA)
                            ->label(Str::title(__('product.smallest_eta')))
                            ->required()
                            ->numeric(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
                    ->label(Str::title(__('product.company_code')))
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy(
                            Company::query()
                                ->select(Company::TABLE_NAME.'.'.Company::BUSINESS_NAME)
                                ->whereColumn(
                                    Company::TABLE_NAME.'.'.Company::CODE,
                                    '=',
                                    Product::TABLE_NAME.'.'.Product::COMPANY_CODE
                                )
                                ->limit(1),
                            $direction
                        );
                    }),

                TextColumn::make('company_branch')
                    ->label(Str::title(__('product.company_code_branch')))
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy(
                            Company::query()
                                ->select(Company::TABLE_NAME.'.'.Company::BRANCH)
                                ->whereColumn(
                                    Company::TABLE_NAME.'.'.Company::CODE,
                                    '=',
                                    Product::TABLE_NAME.'.'.Product::COMPANY_CODE
                                )
                                ->whereColumn(
                                    Company::TABLE_NAME.'.'.Company::CODE_BRANCH,
                                    '=',
                                    Product::TABLE_NAME.'.'.Product::COMPANY_CODE_BRANCH
                                )
                                ->limit(1),
                            $direction
                        );
                    }),

                TextColumn::make(Product::CODE)
                    ->label(Str::title(__('product.code')))
                    ->searchable()
                    ->sortable(),

                TextColumn::make(Product::DESCRIPTION)
                    ->label(Str::title(__('product.description')))
                    ->searchable()
                    ->sortable(),

                TextColumn::make(Product::MEASUREMENT_UNIT)
                    ->label(Str::title(__('product.measurement_unit')))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                TableFilter::make(Product::COMPANY_CODE)
                    ->label(Str::title(__('product.company_code')))
                    ->form([
                        Select::make(Product::COMPANY_CODE)
                            ->label(Str::title(__('product.company_code')))
                            ->options(fn () => Company::all()->sortBy(Company::NAME)->pluck(Company::NAME, Company::CODE)),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data[Product::COMPANY_CODE],
                            fn (Builder $query, string $companyCode): Builder => $query->where(Product::COMPANY_CODE, '=', $companyCode)
                        );
                    }),

                SelectFilter::make(Product::MEASUREMENT_UNIT)
                    ->label(Str::title(__('product.measurement_unit')))
                    ->options(fn () => Product::all()->pluck(Product::MEASUREMENT_UNIT, Product::MEASUREMENT_UNIT)->unique()),
            ])
            ->actions([
                TableEditAction::make(),
            ])
            ->bulkActions([
                TableDeleteBulkAction::make(),
                ExportBulkAction::make()->exports([
                    ExcelExport::make()->fromTable()
                        ->withFilename(fn ($resource) => Str::slug($resource::getPluralModelLabel()).'-'.now()->format('Y-m-d')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return Str::title(__('navigation.quotes'));
    }
}
