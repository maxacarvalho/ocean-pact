<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use App\Rules\CnpjRule;
use App\Utils\Str;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

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
                        Select::make(Supplier::COMPANY_CODE_BRANCH)
                            ->label(Str::formatTitle(__('supplier.branch')))
                            ->relationship('company', 'code_branch')
                            ->getOptionLabelFromRecordUsing(function (Model $record) {
                                return "$record->code_branch - $record->branch";
                            }),

                        TextInput::make(Supplier::STORE)
                            ->label(Str::formatTitle(__('supplier.store')))
                            ->required()
                            ->minLength(1)
                            ->maxLength(2),
                        TextInput::make(Supplier::CODE)
                            ->label(Str::formatTitle(__('supplier.code')))
                            ->required()
                            ->minLength(1)
                            ->maxLength(6),

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
                            ->mask(fn (TextInput\Mask $mask) => $mask->pattern('00.000.000/0000-00')),
                    ]),

                Grid::make(4)
                    ->schema([
                        TextInput::make(Supplier::ADDRESS)
                            ->label(Str::formatTitle(__('supplier.address')))
                            ->required()
                            ->columnSpan(3),
                        TextInput::make(Supplier::NUMBER)
                            ->label(Str::formatTitle(__('supplier.number'))),
                    ]),

                Grid::make(3)
                    ->schema([
                        TextInput::make(Supplier::STATE_CODE)
                            ->label(Str::formatTitle(__('supplier.state_code')))
                            ->required(),
                        TextInput::make(Supplier::STATE_NAME)
                            ->label(Str::formatTitle(__('supplier.state_name'))),
                        TextInput::make(Supplier::POSTAL_CODE)
                            ->label(Str::formatTitle(__('supplier.postal_code')))
                            ->required()
                            ->mask(fn (TextInput\Mask $mask) => $mask->pattern('00000-000')),
                    ]),

                Grid::make(4)
                    ->schema([
                        TextInput::make(Supplier::PHONE_CODE)
                            ->label(Str::formatTitle(__('supplier.phone_code')))
                            ->required(),
                        TextInput::make(Supplier::PHONE_NUMBER)
                            ->label(Str::formatTitle(__('supplier.phone_number')))
                            ->required(),
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
                Tables\Columns\TextColumn::make('company.branch')
                    ->label(Str::formatTitle(__('supplier.branch'))),
                Tables\Columns\TextColumn::make(Supplier::CODE)
                    ->label(Str::formatTitle(__('supplier.code'))),
                Tables\Columns\TextColumn::make(Supplier::STORE)
                    ->label(Str::formatTitle(__('supplier.store'))),
                Tables\Columns\TextColumn::make(Supplier::CNPJ_CPF)
                    ->label(Str::formatTitle(__('supplier.cnpj_cpf'))),
                Tables\Columns\TextColumn::make(Supplier::NAME)
                    ->label(Str::formatTitle(__('supplier.name'))),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
