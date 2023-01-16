<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Company;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class CompaniesRelationManager extends RelationManager
{
    protected static string $relationship = 'companies';

    protected static ?string $recordTitleAttribute = 'description';

    public static function getModelLabel(): string
    {
        return __('company.Company');
    }

    public static function getPluralModelLabel(): string
    {
        return __('company.Companies');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make(Company::DESCRIPTION)
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(Company::CODE)
                    ->label(__('company.Code')),
                Tables\Columns\TextColumn::make(Company::BRANCH)
                    ->label(__('company.Branch')),
                Tables\Columns\TextColumn::make(Company::DESCRIPTION)
                    ->label(__('company.Description')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
