<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Forms\Components\CnpjCpf;
use App\Models\Company;
use App\Utils\Str;
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
        return Str::formatTitle(__('company.companies'));
    }

    public static function getModelLabel(): string
    {
        return Str::formatTitle(__('company.company'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('company.companies'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make(Company::CODE)
                    ->label(Str::formatTitle(__('company.code')))
                    ->required()
                    ->minLength(1)
                    ->maxLength(6),
                TextInput::make(Company::CODE_BRANCH)
                    ->label(Str::formatTitle(__('company.code_branch')))
                    ->required()
                    ->minLength(1)
                    ->maxLength(6),
                TextInput::make(Company::BRANCH)
                    ->label(Str::formatTitle(__('company.branch')))
                    ->required()
                    ->minLength(1)
                    ->maxLength(2),
                TextInput::make(Company::NAME)
                    ->label(Str::formatTitle(__('company.name')))
                    ->required(),
                TextInput::make(Company::BUSINESS_NAME)
                    ->label(Str::formatTitle(__('company.business_name')))
                    ->required(),
                TextInput::make(Company::PHONE_NUMBER)
                    ->label(Str::formatTitle(__('company.phone_number'))),
                TextInput::make(Company::FAX_NUMBER)
                    ->label(Str::formatTitle(__('company.fax_number'))),
                CnpjCpf::make(Company::CNPJ_CPF)
                    ->label(Str::formatTitle(__('company.cnpj_cpf')))
                    ->rule('min:14')
                    ->required()
                    ->unique(table: Company::TABLE_NAME, column: Company::CNPJ_CPF)
                    ->dehydrateStateUsing(function (string $state): string {
                        return preg_replace('/\D/', '', $state);
                    }),
                TextInput::make(Company::STATE_INSCRIPTION)
                    ->label(Str::formatTitle(__('company.state_inscription'))),
                TextInput::make(Company::INSCM)
                    ->label(Str::formatTitle(__('company.inscm'))),
                TextInput::make(Company::ADDRESS)
                    ->label(Str::formatTitle(__('company.address')))
                    ->required(),
                TextInput::make(Company::COMPLEMENT)
                    ->label(Str::formatTitle(__('company.complement'))),
                TextInput::make(Company::NEIGHBORHOOD)
                    ->label(Str::formatTitle(__('company.neighborhood'))),
                TextInput::make(Company::CITY)
                    ->label(Str::formatTitle(__('company.city'))),
                TextInput::make(Company::STATE)
                    ->label(Str::formatTitle(__('company.state'))),
                TextInput::make(Company::POSTAL_CODE)
                    ->label(Str::formatTitle(__('company.postal_code')))
                    ->mask(fn (TextInput\Mask $mask) => $mask->pattern('00000-000')),
                TextInput::make(Company::CITY_CODE)
                    ->label(Str::formatTitle(__('company.city_code'))),
                TextInput::make(Company::CNAE)
                    ->label(Str::formatTitle(__('company.cnae'))),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(Company::CODE)
                    ->label(Str::formatTitle(__('company.code'))),
                Tables\Columns\TextColumn::make(Company::CODE_BRANCH)
                    ->label(Str::formatTitle(__('company.code_branch'))),
                Tables\Columns\TextColumn::make(Company::BRANCH)
                    ->label(Str::formatTitle(__('company.branch'))),
                Tables\Columns\TextColumn::make(Company::CNPJ_CPF)
                    ->label(Str::formatTitle(__('company.cnpj_cpf'))),
                Tables\Columns\TextColumn::make(Company::NAME)
                    ->label(Str::formatTitle(__('company.name'))),
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
