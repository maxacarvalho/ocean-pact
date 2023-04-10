<?php

namespace App\Filament\Resources;

use App\Enums\IntegrationHandlingTypeEnum;
use App\Enums\IntegrationTypeEnum;
use App\Filament\Resources\IntegrationTypeResource\Pages\CreateIntegrationType;
use App\Filament\Resources\IntegrationTypeResource\Pages\EditIntegrationType;
use App\Filament\Resources\IntegrationTypeResource\Pages\ListIntegrationTypes;
use App\Filament\Resources\IntegrationTypeResource\RelationManagers\FieldsRelationManager;
use App\Models\Company;
use App\Models\IntegrationType;
use App\Utils\Str;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteBulkAction as TableDeleteBulkAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                TextColumn::make(IntegrationType::CODE)
                    ->label(Str::formatTitle(__('integration_type.code')))
                    ->sortable()
                    ->searchable(),

                TextColumn::make(IntegrationType::DESCRIPTION)
                    ->label(Str::formatTitle(__('integration_type.description')))
                    ->sortable()
                    ->searchable(),

                TextColumn::make(IntegrationType::TYPE)
                    ->label(Str::formatTitle(__('integration_type.type')))
                    ->sortable()
                    ->searchable(),

                TextColumn::make(IntegrationType::HANDLING_TYPE)
                    ->label(Str::formatTitle(__('integration_type.handling_type')))
                    ->formatStateUsing(fn ($state) => IntegrationHandlingTypeEnum::from($state)->label)
                    ->sortable()
                    ->searchable(),

                TextColumn::make(IntegrationType::TARGET_URL)
                    ->label(Str::formatTitle(__('integration_type.target_url')))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make(IntegrationType::CODE)
                    ->label(Str::formatTitle(__('integration_type.handling_type')))
                    ->options(fn () => IntegrationType::query()->pluck(IntegrationType::CODE, IntegrationType::CODE)),

                SelectFilter::make(IntegrationType::HANDLING_TYPE)
                    ->label(Str::formatTitle(__('integration_type.handling_type')))
                    ->options(IntegrationHandlingTypeEnum::toArray()),
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
            FieldsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIntegrationTypes::route('/'),
            'create' => CreateIntegrationType::route('/create'),
            'edit' => EditIntegrationType::route('/{record}/edit'),
        ];
    }

    public static function getCompanyOptions(): Collection
    {
        return Collection::make([0 => Str::formatTitle(__('company.all'))])
            ->merge(Company::query()->pluck(Company::BRANCH, Company::ID));
    }
}
