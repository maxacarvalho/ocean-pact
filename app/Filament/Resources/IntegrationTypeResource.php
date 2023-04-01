<?php

namespace App\Filament\Resources;

use App\Enums\IntegrationHandlingTypeEnum;
use App\Enums\IntegrationTypeEnum;
use App\Filament\Resources\IntegrationTypeResource\Pages;
use App\Filament\Resources\IntegrationTypeResource\RelationManagers\FieldsRelationManager;
use App\Models\Company;
use App\Models\IntegrationType;
use App\Utils\Str;
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

    protected static ?string $navigationIcon = 'heroicon-o-wifi';

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('integration_type.integration_types'));
    }

    public static function getModelLabel(): string
    {
        return Str::formatTitle(__('integration_type.integration_type'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('integration_type.integration_types'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make(IntegrationType::CODE)
                    ->rules(['nullable', 'string', 'alpha_dash'])
                    ->label(Str::formatTitle(__('integration_type.code')))
                    ->helperText(Str::formatTitle(__('integration_type.code_helper_text'))),
                Select::make(IntegrationType::COMPANY_ID)
                    ->required()
                    ->options(self::getCompanyOptions())
                    ->default(0)
                    ->label(Str::formatTitle(__('integration_type.company')))
                    ->preload(),
                TextInput::make(IntegrationType::DESCRIPTION)
                    ->required()
                    ->label(Str::formatTitle(__('integration_type.description'))),
                Select::make(IntegrationType::TYPE)
                    ->required()
                    ->options(IntegrationTypeEnum::toArray())
                    ->label(Str::formatTitle(__('integration_type.type'))),
                Select::make(IntegrationType::HANDLING_TYPE)
                    ->required()
                    ->options(IntegrationHandlingTypeEnum::toArray())
                    ->label(Str::formatTitle(__('integration_type.handling_type'))),
                TextInput::make(IntegrationType::TARGET_URL)
                    ->required()
                    ->url()
                    ->label(Str::formatTitle(__('integration_type.target_url'))),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(IntegrationType::CODE)
                    ->label(Str::formatTitle(__('integration_type.code'))),
                Tables\Columns\TextColumn::make(IntegrationType::DESCRIPTION)
                    ->label(Str::formatTitle(__('integration_type.description'))),
                Tables\Columns\TextColumn::make(IntegrationType::TYPE)
                    ->label(Str::formatTitle(__('integration_type.type'))),
                Tables\Columns\TextColumn::make(IntegrationType::HANDLING_TYPE)
                    ->label(Str::formatTitle(__('integration_type.handling_type'))),
                Tables\Columns\TextColumn::make(IntegrationType::TARGET_URL)
                    ->label(Str::formatTitle(__('integration_type.target_url'))),
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
            FieldsRelationManager::class,
        ];
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
        return Collection::make([0 => Str::formatTitle(__('company.all'))])
            ->merge(Company::query()->pluck(Company::BRANCH, Company::ID));
    }
}
