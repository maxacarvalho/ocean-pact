<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Company;
use App\Models\Product;
use App\Utils\Str;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
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
                    ->required()
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
                TextColumn::make(Product::RELATION_COMPANY.'.'.Company::CODE_BRANCH)
                    ->label(Str::formatTitle(__('company.code_branch'))),

                TextColumn::make(Product::RELATION_COMPANY.'.'.Company::BRANCH)
                    ->label(Str::formatTitle(__('company.branch'))),

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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    protected static function getNavigationGroup(): ?string
    {
        return Str::formatTitle(__('navigation.quotes'));
    }
}
