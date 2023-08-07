<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages\CreateCompany;
use App\Filament\Resources\CompanyResource\Pages\EditCompany;
use App\Filament\Resources\CompanyResource\Pages\ListCompanies;
use App\Models\Company;
use App\Utils\Str;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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

                TextInput::make(Company::CNPJ_CPF)
                    ->label(Str::formatTitle(__('company.cnpj_cpf')))
                    ->rule('min:14')
                    ->required()
                    ->unique(table: Company::TABLE_NAME, column: Company::CNPJ_CPF)
                    ->dehydrateStateUsing(function (string $state): string {
                        return preg_replace('/\D/', '', $state);
                    })
                    ->mask(RawJs::make(<<<'JS'
                      $input.length >= 15 ? '99.999.999/9999-99' : '999.999.999-99'
                    JS)),

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
                    ->mask('00000-000'),

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
                TextColumn::make(Company::CODE)
                    ->label(Str::formatTitle(__('company.code')))
                    ->sortable()
                    ->searchable(),

                TextColumn::make(Company::CODE_BRANCH)
                    ->label(Str::formatTitle(__('company.code_branch')))
                    ->sortable()
                    ->searchable(),

                TextColumn::make(Company::BRANCH)
                    ->label(Str::formatTitle(__('company.branch')))
                    ->sortable()
                    ->searchable(),

                TextColumn::make(Company::CNPJ_CPF)
                    ->label(Str::formatTitle(__('company.cnpj_cpf')))
                    ->sortable()
                    ->searchable(),

                TextColumn::make(Company::NAME)
                    ->label(Str::formatTitle(__('company.name')))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompanies::route('/'),
            'create' => CreateCompany::route('/create'),
            'edit' => EditCompany::route('/{record}/edit'),
        ];
    }
}
