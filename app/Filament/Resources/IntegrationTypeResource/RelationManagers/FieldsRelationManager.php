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
                Forms\Components\TextInput::make(IntegrationTypeField::FIELD_NAME)
                    ->required()
                    ->label(__('integration_type_field.FieldName')),
                Forms\Components\Select::make(IntegrationTypeField::FIELD_TYPE)
                    ->required()
                    ->options(IntegrationTypeFieldTypeEnum::toArray())
                    ->label(__('integration_type_field.FieldName')),
            ]);
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
