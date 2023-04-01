<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Company;
use App\Utils\Str;
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
        return Str::formatTitle(__('company.company'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('company.companies'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make(Company::BRANCH)
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(Company::CODE)
                    ->label(Str::formatTitle(__('company.code'))),
                Tables\Columns\TextColumn::make(Company::BRANCH)
                    ->label(Str::formatTitle(__('company.branch'))),
                Tables\Columns\TextColumn::make(Company::BRANCH)
                    ->label(Str::formatTitle(__('company.description'))),
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
