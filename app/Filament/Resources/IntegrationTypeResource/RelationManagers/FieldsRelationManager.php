<?php

namespace App\Filament\Resources\IntegrationTypeResource\RelationManagers;

use App\Enums\IntegrationTypeFieldTypeEnum;
use App\Models\IntegrationTypeField;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

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
                                    ->default('Y-m-d')
                                    ->helperText(__('integration_type_field.DateFormatHelperText')),

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
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
