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
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
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

                        Toggle::make(IntegrationType::IS_PROTECTED)
                            ->label(Str::formatTitle(__('integration_type.is_protected')))
                            ->default(fn () => false),

                        Toggle::make(IntegrationType::IS_SYNCHRONOUS)
                            ->label(Str::formatTitle(__('integration_type.is_synchronous')))
                            ->default(fn () => false),

                        Toggle::make(IntegrationType::ALLOWS_DUPLICATES)
                            ->label(Str::formatTitle(__('integration_type.allows_duplicates')))
                            ->default(fn () => false),

                        Select::make(IntegrationType::PROCESSOR)
                            ->label(Str::formatTitle(__('integration_type.processor')))
                            ->columnSpan(3)
                            ->options(function () {
                                return collect(scandir(app_path('Jobs/PayloadProcessors')))
                                    ->filter(fn ($file) => ! in_array($file, ['.', '..']))
                                    ->map(fn ($file) => 'App\Jobs\PayloadProcessors\\'.preg_replace('/\\.[^.\\s]{3,4}$/', '', $file))
                                    ->filter(fn ($class) => class_exists($class))
                                    ->filter(fn ($class) => is_subclass_of($class, 'App\Jobs\PayloadProcessors\PayloadProcessor'))
                                    ->map(fn ($class) => [
                                        'label' => $class,
                                        'value' => $class,
                                    ])
                                    ->mapWithKeys(fn (array $item, int $key) => [$item['label'] => $item['value']])
                                    ->toArray();
                            }),
                    ]),
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
