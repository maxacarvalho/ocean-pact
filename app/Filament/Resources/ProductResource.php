<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Models\Company;
use App\Models\Product;
use App\Utils\Str;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteBulkAction as TableDeleteBulkAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter as TableFilter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Database\Query\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('product.products'));
    }

    public static function getModelLabel(): string
    {
        return Str::formatTitle(__('product.product'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('product.products'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make(Product::COMPANY_CODE)
                    ->label(Str::formatTitle(__('product.company_code')))
                    ->relationship(Product::RELATION_COMPANY, Company::NAME)
                    ->reactive(),

                Select::make(Product::COMPANY_CODE_BRANCH)
                    ->label(Str::formatTitle(__('product.company_code_branch')))
                    ->options(function (Closure $get) {
                        $companyCode = $get(Product::COMPANY_CODE);

                        if (null === $companyCode) {
                            return [];
                        }

                        return Company::query()
                            ->where(Company::CODE, '=', $companyCode)
                            ->pluck(Company::BRANCH, Company::CODE_BRANCH)
                            ->toArray();
                    }),

                Forms\Components\TextInput::make(Product::CODE)
                    ->label(Str::formatTitle(__('product.code')))
                    ->required(),

                Forms\Components\TextInput::make(Product::DESCRIPTION)
                    ->label(Str::formatTitle(__('product.description')))
                    ->required(),

                Forms\Components\TextInput::make(Product::MEASUREMENT_UNIT)
                    ->label(Str::formatTitle(__('product.measurement_unit')))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
                    ->label(Str::formatTitle(__('product.company_code')))
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
                    ->label(Str::formatTitle(__('product.company_code_branch')))
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
                    ->label(Str::formatTitle(__('product.code')))
                    ->searchable()
                    ->sortable(),

                TextColumn::make(Product::DESCRIPTION)
                    ->label(Str::formatTitle(__('product.description')))
                    ->searchable()
                    ->sortable(),

                TextColumn::make(Product::MEASUREMENT_UNIT)
                    ->label(Str::formatTitle(__('product.measurement_unit')))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                TableFilter::make(Product::COMPANY_CODE)
                    ->label(Str::formatTitle(__('product.company_code')))
                    ->form([
                        Select::make(Product::COMPANY_CODE)
                            ->label(Str::formatTitle(__('product.company_code')))
                            ->options(fn () => Company::all()->sortBy(Company::NAME)->pluck(Company::NAME, Company::CODE)),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data[Product::COMPANY_CODE],
                            fn (Builder $query, string $companyCode): Builder => $query->where(Product::COMPANY_CODE, '=', $companyCode)
                        );
                    }),

                SelectFilter::make(Product::MEASUREMENT_UNIT)
                    ->label(Str::formatTitle(__('product.measurement_unit')))
                    ->options(fn () => Product::all()->pluck(Product::MEASUREMENT_UNIT, Product::MEASUREMENT_UNIT)->unique()),
            ])
            ->actions([
                TableEditAction::make(),
            ])
            ->bulkActions([
                TableDeleteBulkAction::make(),
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

    protected static function getNavigationGroup(): ?string
    {
        return Str::formatTitle(__('navigation.quotes'));
    }
}
