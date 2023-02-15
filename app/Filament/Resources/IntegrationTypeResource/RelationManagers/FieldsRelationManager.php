<?php

namespace App\Filament\Resources\IntegrationTypeResource\RelationManagers;

use App\Enums\IntegrationTypeFieldTypeEnum;
use App\Models\IntegrationTypeField;
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
        return __('integration_type_field.IntegrationTypeFields');
    }

    public static function getModelLabel(): string
    {
        return __('integration_type_field.IntegrationTypeField');
    }

    public static function getPluralModelLabel(): string
    {
        return __('integration_type_field.IntegrationTypeFields');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('integration_type_field.General'))
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make(IntegrationTypeField::FIELD_NAME)
                            ->label(__('integration_type_field.FieldName'))
                            ->required(),
                        Forms\Components\Select::make(IntegrationTypeField::FIELD_TYPE)
                            ->label(__('integration_type_field.FieldType'))
                            ->required()
                            ->reactive()
                            ->options(IntegrationTypeFieldTypeEnum::toArray()),
                    ]),

                Forms\Components\Section::make(__('integration_type_field.Rules'))
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
                                    ->label(__('integration_type_field.Required'))
                                    ->default(true),

                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.array')
                                    ->label(__('integration_type_field.Array')),

                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.email')
                                    ->label(__('integration_type_field.Email')),

                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.alpha')
                                    ->label(__('integration_type_field.Alpha')),

                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.alpha_num')
                                    ->label(__('integration_type_field.AlphaNum')),

                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.alpha_dash')
                                    ->label(__('integration_type_field.AlphaDash')),

                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.nullable')
                                    ->label(__('integration_type_field.Nullable')),

                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.present')
                                    ->label(__('integration_type_field.Present')),

                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.lowercase')
                                    ->label(__('integration_type_field.Lowercase')),

                                Forms\Components\Toggle::make(IntegrationTypeField::FIELD_RULES.'.uppercase')
                                    ->label(__('integration_type_field.Uppercase')),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make(IntegrationTypeField::FIELD_RULES.'.date_format')
                                    ->label(__('integration_type_field.DateFormat'))
                                    ->helperText(function () {
                                        return new HtmlString('<a href="https://www.php.net/manual/pt_BR/datetime.formats.php" target="_blank">'.__('integration_type_field.DateFormatHelperText').'</a>');
                                    })
                                    ->required(function (Closure $get) {
                                        $fieldType = $get(IntegrationTypeField::FIELD_TYPE);

                                        if ($fieldType instanceof IntegrationTypeFieldTypeEnum && $fieldType->equals(IntegrationTypeFieldTypeEnum::date())) {
                                            return true;
                                        }

                                        if ($fieldType === IntegrationTypeFieldTypeEnum::date()->value) {
                                            return true;
                                        }

                                        return false;
                                    }),

                                Forms\Components\TextInput::make(IntegrationTypeField::FIELD_RULES.'.starts_with')
                                    ->label(__('integration_type_field.StartsWith'))
                                    ->helperText(__('integration_type_field.StartsWithHelperText')),

                                Forms\Components\TextInput::make(IntegrationTypeField::FIELD_RULES.'.digits')
                                    ->label(__('integration_type_field.Digits'))
                                    ->numeric(),

                                Forms\Components\TextInput::make(IntegrationTypeField::FIELD_RULES.'.digits_between')
                                    ->label(__('integration_type_field.DigitsBetween'))
                                    ->helperText(__('integration_type_field.DigitsBetweenHelperText'))
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
