<?php

namespace App\Filament\Resources;

use App\Enums\IntegrationHandlingTypeEnum;
use App\Enums\IntegrationTypeEnum;
use App\Filament\Resources\IntegrationTypeResource\Pages;
use App\Models\Company;
use App\Models\IntegrationType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Collection;

class IntegrationTypeResource extends Resource
{
    protected static ?string $model = IntegrationType::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function getNavigationLabel(): string
    {
        return __('integration_type.IntegrationTypes');
    }

    public static function getModelLabel(): string
    {
        return __('integration_type.IntegrationType');
    }

    public static function getPluralModelLabel(): string
    {
        return __('integration_type.IntegrationTypes');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make(IntegrationType::CODE)
                    ->rules(['nullable', 'string', 'alpha_dash'])
                    ->label(__('integration_type.Code')),
                Select::make(IntegrationType::COMPANY_ID)
                    ->required()
                    ->options(self::getCompanyOptions())
                    ->default(0)
                    ->label(__('integration_type.Company'))
                    ->preload(),
                TextInput::make(IntegrationType::DESCRIPTION)
                    ->required()
                    ->label(__('integration_type.Description')),
                Select::make(IntegrationType::TYPE)
                    ->required()
                    ->options(IntegrationTypeEnum::toArray())
                    ->label(__('integration_type.Type')),
                Select::make(IntegrationType::HANDLING_TYPE)
                    ->required()
                    ->options(IntegrationHandlingTypeEnum::toArray())
                    ->label(__('integration_type.HandlingType')),
                TextInput::make(IntegrationType::TARGET_URL)
                    ->required()
                    ->url()
                    ->label(__('integration_type.TargetURL')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(IntegrationType::CODE)
                    ->label(__('integration_type.Code')),
                Tables\Columns\TextColumn::make(IntegrationType::DESCRIPTION)
                    ->label(__('integration_type.Description')),
                Tables\Columns\TextColumn::make(IntegrationType::TYPE)
                    ->label(__('integration_type.Type')),
                Tables\Columns\TextColumn::make(IntegrationType::HANDLING_TYPE)
                    ->label(__('integration_type.HandlingType')),
                Tables\Columns\TextColumn::make(IntegrationType::TARGET_URL)
                    ->label(__('integration_type.TargetURL')),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIntegrationTypes::route('/'),
            'create' => Pages\CreateIntegrationType::route('/create'),
            'edit' => Pages\EditIntegrationType::route('/{record}/edit'),
        ];
    }

    public static function getCompanyOptions(): Collection
    {
        return Collection::make([0 => __('company.All')])
            ->merge(Company::query()->pluck(Company::DESCRIPTION, Company::ID));
    }
}
