<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages\CreateCurrency;
use App\Filament\Resources\CurrencyResource\Pages\EditCurrency;
use App\Filament\Resources\CurrencyResource\Pages\ListCurrencies;
use App\Models\Company;
use App\Models\Currency;
use App\Utils\Str;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('currency.currencies'));
    }

    public static function getModelLabel(): string
    {
        return Str::formatTitle(__('currency.currency'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('currency.currencies'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make(Currency::COMPANY_CODE)
                    ->label(Str::formatTitle(__('currency.company_code')))
                    ->options(fn () => Company::query()->distinct()->pluck(Company::CODE_AND_BUSINESS_NAME, Company::CODE)->toArray())
                    ->required(),

                TextInput::make(Currency::PROTHEUS_CURRENCY_ID)
                    ->label(Str::formatTitle(__('currency.protheus_currency_id')))
                    ->required()
                    ->integer(),

                TextInput::make(Currency::DESCRIPTION)
                    ->label(Str::formatTitle(__('currency.description')))
                    ->required(),

                TextInput::make(Currency::PROTHEUS_CODE)
                    ->label(Str::formatTitle(__('currency.protheus_code')))
                    ->required(),

                TextInput::make(Currency::PROTHEUS_ACRONYM)
                    ->label(Str::formatTitle(__('currency.protheus_acronym')))
                    ->required(),

                TextInput::make(Currency::ISO_CODE)
                    ->label(Str::formatTitle(__('currency.iso_code')))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(Currency::PROTHEUS_CURRENCY_ID)
                    ->label(Str::formatTitle(__('currency.protheus_currency_id'))),

                TextColumn::make(Currency::DESCRIPTION)
                    ->label(Str::formatTitle(__('currency.description'))),

                TextColumn::make(Currency::PROTHEUS_CODE)
                    ->label(Str::formatTitle(__('currency.protheus_code'))),

                TextColumn::make(Currency::PROTHEUS_ACRONYM)
                    ->label(Str::formatTitle(__('currency.protheus_acronym'))),

                TextColumn::make(Currency::ISO_CODE)
                    ->label(Str::formatTitle(__('currency.iso_code'))),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCurrencies::route('/'),
            'create' => CreateCurrency::route('/create'),
            'edit' => EditCurrency::route('/{record}/edit'),
        ];
    }
}
