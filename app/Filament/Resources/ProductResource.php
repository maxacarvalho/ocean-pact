<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Models\Company;
use App\Models\Product;
use App\Utils\Str;
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
use Illuminate\Database\Eloquent\Model;

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
                Select::make(Product::COMPANY_ID)
                    ->label(Str::formatTitle(__('product.company_id')))
                    ->relationship(Product::RELATION_COMPANY, Company::CODE_BRANCH)
                    ->getOptionLabelFromRecordUsing(function (Model|Company $record) {
                        return "$record->code_branch - $record->branch";
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
                TextColumn::make('company_info')
                    ->label(Str::formatTitle(__('product.company')))
                    ->sortable([Company::CODE_BRANCH, Company::BRANCH])
                    ->formatStateUsing(function (?string $state, Model|Product|Company $record): ?string {
                        return "{$record->code_branch} {$record->branch}";
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
                TableFilter::make(Product::COMPANY_ID)
                    ->label(Str::formatTitle(__('budget.company_id')))
                    ->form([
                        Select::make(Product::COMPANY_ID)
                            ->label(Str::formatTitle(__('budget.company_id')))
                            ->options(fn () => Company::all()->pluck(Company::CODE_BRANCH_AND_BRANCH, Company::ID)),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data[Product::COMPANY_ID],
                            fn (Builder $query, int $companyId): Builder => $query->where(Product::COMPANY_ID, '=', $companyId)
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
