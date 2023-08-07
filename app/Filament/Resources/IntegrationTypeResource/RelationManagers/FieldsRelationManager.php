<?php

namespace App\Filament\Resources\IntegrationTypeResource\RelationManagers;

use App\Enums\IntegrationTypeFieldTypeEnum;
use App\Models\IntegrationTypeField;
use App\Rules\MultipleEmailsRule;
use App\Utils\Str;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Filament\Tables\Actions\DeleteAction as TableDeleteAction;
use Filament\Tables\Actions\DeleteBulkAction as TableDeleteBulkAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class FieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'fields';

    protected static ?string $recordTitleAttribute = 'field_name';

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('integration_type_field.integration_type_fields'));
    }

    public static function getModelLabel(): ?string
    {
        return Str::formatTitle(__('integration_type_field.integration_type_field'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('integration_type_field.integration_type_fields'));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(Str::formatTitle(__('integration_type_field.general')))
                    ->collapsible()
                    ->schema([
                        TextInput::make(IntegrationTypeField::FIELD_NAME)
                            ->label(Str::formatTitle(__('integration_type_field.field_name')))
                            ->required(),
                        Select::make(IntegrationTypeField::FIELD_TYPE)
                            ->label(Str::formatTitle(__('integration_type_field.field_type')))
                            ->required()
                            ->reactive()
                            ->options(IntegrationTypeFieldTypeEnum::class)
                            ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                                if (IntegrationTypeFieldTypeEnum::array === $state) {
                                    $set(IntegrationTypeField::FIELD_RULES.'.array', true);
                                }
                            }),
                    ]),

                Section::make(Str::formatTitle(__('integration_type_field.rules')))
                    ->collapsible()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Toggle::make(IntegrationTypeField::FIELD_RULES.'.required')
                                    ->label(Str::formatTitle(__('integration_type_field.required')))
                                    ->default(true)
                                    ->reactive()
                                    ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                                        $set(IntegrationTypeField::FIELD_RULES.'.nullable', ! $state);
                                    }),

                                Toggle::make(IntegrationTypeField::FIELD_RULES.'.array')
                                    ->label(Str::formatTitle(__('integration_type_field.array'))),

                                Toggle::make(IntegrationTypeField::FIELD_RULES.'.email')
                                    ->label(Str::formatTitle(__('integration_type_field.email'))),

                                Toggle::make(IntegrationTypeField::FIELD_RULES.'.alpha')
                                    ->label(Str::formatTitle(__('integration_type_field.alpha'))),

                                Toggle::make(IntegrationTypeField::FIELD_RULES.'.alpha_num')
                                    ->label(Str::formatTitle(__('integration_type_field.alpha_num'))),

                                Toggle::make(IntegrationTypeField::FIELD_RULES.'.alpha_dash')
                                    ->label(Str::formatTitle(__('integration_type_field.alpha_dash'))),

                                Toggle::make(IntegrationTypeField::FIELD_RULES.'.nullable')
                                    ->label(Str::formatTitle(__('integration_type_field.nullable'))),

                                Toggle::make(IntegrationTypeField::FIELD_RULES.'.present')
                                    ->label(Str::formatTitle(__('integration_type_field.present'))),

                                Toggle::make(IntegrationTypeField::FIELD_RULES.'.lowercase')
                                    ->label(Str::formatTitle(__('integration_type_field.lowercase'))),

                                Toggle::make(IntegrationTypeField::FIELD_RULES.'.uppercase')
                                    ->label(Str::formatTitle(__('integration_type_field.uppercase'))),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make(IntegrationTypeField::FIELD_RULES.'.date_format')
                                    ->label(Str::formatTitle(__('integration_type_field.date_format')))
                                    ->helperText(function () {
                                        return new HtmlString('<a href="https://www.php.net/manual/pt_BR/datetime.formats.php" target="_blank">'.Str::lcfirst(__('integration_type_field.date_format_helper_text')).'</a>');
                                    })
                                    ->required(function (\Filament\Forms\Get $get) {
                                        $fieldType = $get(IntegrationTypeField::FIELD_TYPE);

                                        if (
                                            $fieldType instanceof IntegrationTypeFieldTypeEnum
                                            && $fieldType === IntegrationTypeFieldTypeEnum::date
                                        ) {
                                            return true;
                                        }

                                        if ($fieldType === IntegrationTypeFieldTypeEnum::date) {
                                            return true;
                                        }

                                        return false;
                                    }),

                                TextInput::make(IntegrationTypeField::FIELD_RULES.'.starts_with')
                                    ->label(Str::formatTitle(__('integration_type_field.starts_with')))
                                    ->helperText(Str::lcfirst(__('integration_type_field.starts_with_helper_text'))),

                                TextInput::make(IntegrationTypeField::FIELD_RULES.'.digits')
                                    ->label(Str::formatTitle(__('integration_type_field.digits')))
                                    ->numeric(),

                                TextInput::make(IntegrationTypeField::FIELD_RULES.'.digits_between')
                                    ->label(Str::formatTitle(__('integration_type_field.digits_between')))
                                    ->helperText(Str::lcfirst(__('integration_type_field.digits_between_helper_text')))
                                    ->regex('/^([0-9]+),([0-9]+)$/'),

                                TextInput::make(IntegrationTypeField::FIELD_RULES.'.max')
                                    ->label(Str::formatTitle(__('integration_type_field.max'))),

                                Select::make(IntegrationTypeField::FIELD_RULES.'.custom')
                                    ->label(Str::formatTitle(__('integration_type_field.max')))
                                    ->multiple()
                                    ->options([
                                        MultipleEmailsRule::class => MultipleEmailsRule::class,
                                    ]),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(IntegrationTypeField::FIELD_NAME)
                    ->label(Str::formatTitle(__('integration_type_field.field_name'))),
                TextColumn::make(IntegrationTypeField::FIELD_TYPE)
                    ->label(Str::formatTitle(__('integration_type_field.field_type'))),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                TableCreateAction::make()->mutateFormDataUsing(fn (array $data) => self::prepareData($data)),
            ])
            ->actions([
                TableEditAction::make()->mutateFormDataUsing(fn (array $data) => self::prepareData($data)),
                TableDeleteAction::make(),
            ])
            ->bulkActions([
                TableDeleteBulkAction::make(),
            ]);
    }

    private static function prepareData(array $data): array
    {
        $data[IntegrationTypeField::FIELD_RULES] = array_filter($data[IntegrationTypeField::FIELD_RULES]);

        $fieldType = $data[IntegrationTypeField::FIELD_TYPE];

        if ($fieldType === IntegrationTypeFieldTypeEnum::float) {
            unset($data[IntegrationTypeField::FIELD_RULES]['numeric']);
            $data[IntegrationTypeField::FIELD_RULES] = ['numeric' => true] + $data[IntegrationTypeField::FIELD_RULES];
        }

        if ($fieldType === IntegrationTypeFieldTypeEnum::integer) {
            unset($data[IntegrationTypeField::FIELD_RULES]['integer']);
            $data[IntegrationTypeField::FIELD_RULES] = ['integer' => true] + $data[IntegrationTypeField::FIELD_RULES];
        }

        if ($fieldType === IntegrationTypeFieldTypeEnum::boolean) {
            unset($data[IntegrationTypeField::FIELD_RULES]['boolean']);
            $data[IntegrationTypeField::FIELD_RULES] = ['boolean' => true] + $data[IntegrationTypeField::FIELD_RULES];
        }

        if ($fieldType === IntegrationTypeFieldTypeEnum::string) {
            unset($data[IntegrationTypeField::FIELD_RULES]['string']);
            $data[IntegrationTypeField::FIELD_RULES] = ['string' => true] + $data[IntegrationTypeField::FIELD_RULES];
        }

        return $data;
    }
}
