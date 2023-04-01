<?php

namespace App\Filament\Resources\IntegrationTypeResource\RelationManagers;

use App\Enums\IntegrationTypeFieldTypeEnum;
use App\Models\IntegrationTypeField;
use App\Utils\Str;
use Closure;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\HtmlString;

class FieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'fields';

    protected static ?string $recordTitleAttribute = 'field_name';

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('integration_type_field.integration_type_fields'));
    }

    public static function getModelLabel(): string
    {
        return Str::formatTitle(__('integration_type_field.integration_type_field'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('integration_type_field.integration_type_fields'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(Str::formatTitle(__('integration_type_field.general')))
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make(IntegrationTypeField::FIELD_NAME)
                            ->label(Str::formatTitle(__('integration_type_field.field_name')))
                            ->required(),
                        Forms\Components\Select::make(IntegrationTypeField::FIELD_TYPE)
                            ->label(Str::formatTitle(__('integration_type_field.field_type')))
                            ->required()
                            ->reactive()
                            ->options(IntegrationTypeFieldTypeEnum::toArray()),
                    ]),

                Forms\Components\Section::make(Str::formatTitle(__('integration_type_field.rules')))
                    ->collapsible()
                    ->collapsed(function (Closure $get) {
                        return is_null($get(IntegrationTypeField::FIELD_TYPE));
                    })
                    ->disabled(function (Closure $get) {
                        return is_null($get(IntegrationTypeField::FIELD_TYPE));
                    })
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.required')
                                    ->label(Str::formatTitle(__('integration_type_field.required')))
                                    ->default(true),

                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.array')
                                    ->label(Str::formatTitle(__('integration_type_field.array'))),

                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.email')
                                    ->label(Str::formatTitle(__('integration_type_field.email'))),

                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.alpha')
                                    ->label(Str::formatTitle(__('integration_type_field.alpha'))),

                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.alpha_num')
                                    ->label(Str::formatTitle(__('integration_type_field.alpha_num'))),

                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.alpha_dash')
                                    ->label(Str::formatTitle(__('integration_type_field.alpha_dash'))),

                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.nullable')
                                    ->label(Str::formatTitle(__('integration_type_field.nullable'))),

                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.present')
                                    ->label(Str::formatTitle(__('integration_type_field.present'))),

                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.lowercase')
                                    ->label(Str::formatTitle(__('integration_type_field.lowercase'))),

                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.uppercase')
                                    ->label(Str::formatTitle(__('integration_type_field.uppercase'))),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make(IntegrationTypeField::FIELD_RULES.'.date_format')
                                    ->label(Str::formatTitle(__('integration_type_field.date_format')))
                                    ->helperText(function () {
                                        return new HtmlString('<a href="https://www.php.net/manual/pt_BR/datetime.formats.php" target="_blank">'.Str::lcfirst(__('integration_type_field.date_format_helper_text')).'</a>');
                                    })
                                    ->required(function (Closure $get) {
                                        $fieldType = $get(IntegrationTypeField::FIELD_TYPE);

                                        if (
                                            $fieldType instanceof IntegrationTypeFieldTypeEnum
                                            && $fieldType->equals(IntegrationTypeFieldTypeEnum::date())
                                        ) {
                                            return true;
                                        }

                                        if ($fieldType === IntegrationTypeFieldTypeEnum::date()->value) {
                                            return true;
                                        }

                                        return false;
                                    }),

                                Forms\Components\TextInput::make(IntegrationTypeField::FIELD_RULES.'.starts_with')
                                    ->label(Str::formatTitle(__('integration_type_field.starts_with')))
                                    ->helperText(Str::lcfirst(__('integration_type_field.starts_with_helper_text'))),

                                Forms\Components\TextInput::make(IntegrationTypeField::FIELD_RULES.'.digits')
                                    ->label(Str::formatTitle(__('integration_type_field.digits')))
                                    ->numeric(),

                                Forms\Components\TextInput::make(IntegrationTypeField::FIELD_RULES.'.digits_between')
                                    ->label(Str::formatTitle(__('integration_type_field.digits_between')))
                                    ->helperText(Str::lcfirst(__('integration_type_field.digits_between_helper_text')))
                                    ->regex('/^([0-9]+),([0-9]+)$/'),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(IntegrationTypeField::FIELD_NAME)
                    ->label(__('integration_type_field.FieldName')),
                Tables\Columns\TextColumn::make(IntegrationTypeField::FIELD_TYPE)
                    ->label(__('integration_type_field.FieldType')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->mutateFormDataUsing(fn (array $data) => self::prepareData($data)),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->mutateFormDataUsing(fn (array $data) => self::prepareData($data)),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    private static function prepareData(array $data): array
    {
        $data[IntegrationTypeField::FIELD_RULES] = array_filter($data[IntegrationTypeField::FIELD_RULES]);

        $fieldType = $data[IntegrationTypeField::FIELD_TYPE];

        if ($fieldType === IntegrationTypeFieldTypeEnum::float()->value) {
            unset($data[IntegrationTypeField::FIELD_RULES]['numeric']);
            $data[IntegrationTypeField::FIELD_RULES] = ['numeric' => true] + $data[IntegrationTypeField::FIELD_RULES];
        }

        if ($fieldType === IntegrationTypeFieldTypeEnum::integer()->value) {
            unset($data[IntegrationTypeField::FIELD_RULES]['integer']);
            $data[IntegrationTypeField::FIELD_RULES] = ['integer' => true] + $data[IntegrationTypeField::FIELD_RULES];
        }

        if ($fieldType === IntegrationTypeFieldTypeEnum::boolean()->value) {
            unset($data[IntegrationTypeField::FIELD_RULES]['boolean']);
            $data[IntegrationTypeField::FIELD_RULES] = ['boolean' => true] + $data[IntegrationTypeField::FIELD_RULES];
        }

        if ($fieldType === IntegrationTypeFieldTypeEnum::string()->value) {
            unset($data[IntegrationTypeField::FIELD_RULES]['string']);
            $data[IntegrationTypeField::FIELD_RULES] = ['string' => true] + $data[IntegrationTypeField::FIELD_RULES];
        }

        return $data;
    }
}
