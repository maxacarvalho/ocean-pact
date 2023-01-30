<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use App\Rules\Cnpj;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function getNavigationLabel(): string
    {
        return __('company.Companies');
    }

    public static function getModelLabel(): string
    {
        return __('company.Company');
    }

    public static function getPluralModelLabel(): string
    {
        return __('company.Companies');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make(Company::CODE)
                    ->required()
                    ->minLength(2)
                    ->maxLength(4)
                    ->label(__('company.Code')),
                TextInput::make(Company::BRANCH)
                    ->required()
                    ->minLength(2)
                    ->maxLength(4)
                    ->label(__('company.Branch')),
                TextInput::make(Company::CNPJ)
                    ->required()
                    ->unique(table: Company::TABLE_NAME, column: Company::CNPJ)
                    ->rules([new Cnpj()])
                    ->mask(fn (TextInput\Mask $mask) => $mask->pattern('00.000.000/0000-00'))
                    ->label(__('company.CNPJ')),
                TextInput::make(Company::DESCRIPTION)
                    ->required()
                    ->label(__('company.Description')),
                TextInput::make(Company::LEGAL_NAME)
                    ->required()
                    ->label(__('company.LegalName')),
                TextInput::make(Company::TRADE_NAME)
                    ->required()
                    ->label(__('company.TradeName')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(Company::CODE)
                    ->label(__('company.Code')),
                Tables\Columns\TextColumn::make(Company::BRANCH)
                    ->label(__('company.Branch')),
                Tables\Columns\TextColumn::make(Company::CNPJ)
                    ->label(__('company.CNPJ')),
                Tables\Columns\TextColumn::make(Company::DESCRIPTION)
                    ->label(__('company.Description')),
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
