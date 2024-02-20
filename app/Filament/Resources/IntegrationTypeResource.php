<?php

namespace App\Filament\Resources;

use App\Enums\IntegraHub\IntegrationHandlingTypeEnum;
use App\Enums\IntegraHub\IntegrationTypeEnum;
use App\Enums\IntegraHub\IntegrationTypeSchedulingOptionsEnum;
use App\Filament\Resources\IntegrationTypeResource\Pages\CreateIntegrationType;
use App\Filament\Resources\IntegrationTypeResource\Pages\EditIntegrationType;
use App\Filament\Resources\IntegrationTypeResource\Pages\ListIntegrationTypes;
use App\Filament\Resources\IntegrationTypeResource\RelationManagers\FieldsRelationManager;
use App\Models\IntegraHub\IntegrationType;
use App\Models\QuotesPortal\Company;
use App\Rules\CronExpressionRule;
use App\Utils\Str;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction as TableDeleteBulkAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

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
                    ->label(Str::formatTitle(__('integration_type.code')))
                    ->rules(['nullable', 'string', 'alpha_dash'])
                    ->helperText(Str::formatTitle(__('integration_type.code_helper_text'))),

                Select::make(IntegrationType::COMPANY_ID)
                    ->label(Str::formatTitle(__('integration_type.company')))
                    ->options(fn () => self::getCompanyOptions())
                    ->default(0)
                    ->preload(),

                TextInput::make(IntegrationType::DESCRIPTION)
                    ->label(Str::formatTitle(__('integration_type.description')))
                    ->required(),

                Select::make(IntegrationType::TYPE)
                    ->label(Str::formatTitle(__('integration_type.type')))
                    ->required()
                    ->options(IntegrationTypeEnum::class),

                Select::make(IntegrationType::HANDLING_TYPE)
                    ->label(Str::formatTitle(__('integration_type.handling_type')))
                    ->options(IntegrationHandlingTypeEnum::class),

                TextInput::make(IntegrationType::TARGET_URL)
                    ->label(Str::formatTitle(__('integration_type.target_url')))
                    ->url(),

                KeyValue::make(IntegrationType::HEADERS)
                    ->label(Str::formatTitle(__('integration_type.headers'))),

                Repeater::make(IntegrationType::PATH_PARAMETERS)
                    ->label(Str::formatTitle(__('integration_type.path_parameters')))
                    ->schema([
                        TextInput::make('parameter')
                            ->label(Str::formatTitle(__('integration_type.parameter')))
                            ->required(),
                    ]),

                Fieldset::make(Str::formatTitle(__('integration_type.system_settings')))
                    ->visible(fn () => Auth::user()->isSuperAdmin())
                    ->columns(5)
                    ->schema([
                        Toggle::make(IntegrationType::IS_VISIBLE)
                            ->label(Str::formatTitle(__('integration_type.is_visible')))
                            ->default(fn () => true),

                        Toggle::make(IntegrationType::IS_ENABLED)
                            ->label(Str::formatTitle(__('integration_type.is_enabled')))
                            ->default(fn () => true),

                        Toggle::make(IntegrationType::IS_SYNCHRONOUS)
                            ->label(Str::formatTitle(__('integration_type.is_synchronous')))
                            ->default(fn () => false),

                        Toggle::make(IntegrationType::ALLOWS_DUPLICATES)
                            ->label(Str::formatTitle(__('integration_type.allows_duplicates')))
                            ->default(fn () => false),
                    ]),

                Fieldset::make(Str::formatTitle(__('integration_type.scheduling_settings')))
                    ->visible(fn () => Auth::user()->isSuperAdmin() || Auth::user()->isAdmin())
                    ->columns(5)
                    ->schema([
                        Select::make('scheduling_settings.frequency')
                            ->label(Str::formatTitle(__('integration_type.scheduling_settings.frequency')))
                            ->options(IntegrationTypeSchedulingOptionsEnum::class)
                            ->live(),
                        TextInput::make('scheduling_settings.expression')
                            ->label(Str::formatTitle(__('integration_type.scheduling_settings.expression')))
                            ->visible(function (Get $get) {
                                if (! $get('scheduling_settings.frequency')) {
                                    return false;
                                }
                                $frequency = IntegrationTypeSchedulingOptionsEnum::from($get('scheduling_settings.frequency'));
                                return $frequency === IntegrationTypeSchedulingOptionsEnum::custom;
                            })
                            ->rules([new CronExpressionRule()]),
                        TimePicker::make('scheduling_settings.time')
                            ->label(Str::formatTitle(__('integration_type.scheduling_settings.time')))
                            ->visible(function (Get $get) {
                                if (! $get('scheduling_settings.frequency')) {
                                    return false;
                                }
                                $frequency = IntegrationTypeSchedulingOptionsEnum::from($get('scheduling_settings.frequency'));
                                return $frequency === IntegrationTypeSchedulingOptionsEnum::daily;
                            })
                    ])
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
                    ->options(IntegrationHandlingTypeEnum::class),
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
