<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages\CreateSupplier;
use App\Filament\Resources\SupplierResource\Pages\EditSupplier;
use App\Filament\Resources\SupplierResource\Pages\ListSuppliers;
use App\Filament\Resources\SupplierResource\RelationManagers\CompaniesRelationManager;
use App\Models\Supplier;
use App\Rules\CnpjRule;
use App\Utils\Str;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('supplier.suppliers'));
    }

    public static function getModelLabel(): string
    {
        return Str::formatTitle(__('supplier.supplier'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('supplier.suppliers'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Grid::make()
                    ->schema([
                        TextInput::make(Supplier::STORE)
                            ->label(Str::formatTitle(__('supplier.store')))
                            ->required()
                            ->minLength(1)
                            ->maxLength(10),

                        TextInput::make(Supplier::CODE)
                            ->label(Str::formatTitle(__('supplier.code')))
                            ->required()
                            ->minLength(1)
                            ->maxLength(10),

                        TextInput::make(Supplier::NAME)
                            ->label(Str::formatTitle(__('supplier.name')))
                            ->required(),

                        TextInput::make(Supplier::BUSINESS_NAME)
                            ->label(Str::formatTitle(__('supplier.business_name')))
                            ->required(),

                        TextInput::make(Supplier::CNPJ_CPF)
                            ->label(Str::formatTitle(__('supplier.cnpj_cpf')))
                            ->required()
                            ->unique(table: Supplier::TABLE_NAME, column: Supplier::CNPJ_CPF)
                            ->rules([new CnpjRule()])
                            ->mask('00.000.000/0000-00'),
                    ]),

                Grid::make()
                    ->schema([
                        TextInput::make(Supplier::CONTACT)
                            ->label(Str::formatTitle(__('supplier.contact')))
                            ->required(),

                        TextInput::make(Supplier::EMAIL)
                            ->label(Str::formatTitle(__('supplier.email')))
                            ->required()
                            ->email(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(Supplier::CODE)
                    ->label(Str::formatTitle(__('supplier.code')))
                    ->sortable()
                    ->searchable(),

                TextColumn::make(Supplier::STORE)
                    ->label(Str::formatTitle(__('supplier.store')))
                    ->sortable()
                    ->searchable(),

                TextColumn::make(Supplier::CNPJ_CPF)
                    ->label(Str::formatTitle(__('supplier.cnpj_cpf')))
                    ->sortable()
                    ->searchable(),

                TextColumn::make(Supplier::NAME)
                    ->label(Str::formatTitle(__('supplier.name')))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
            ])
            ->actions([
                TableEditAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CompaniesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuppliers::route('/'),
            'create' => CreateSupplier::route('/create'),
            'edit' => EditSupplier::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return Str::formatTitle(__('navigation.quotes'));
    }
}
