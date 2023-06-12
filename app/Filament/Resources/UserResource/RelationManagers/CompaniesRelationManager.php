<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Utils\Str;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables\Actions\AttachAction as TableAttachAction;
use Filament\Tables\Actions\DetachAction as TableDetachAction;
use Filament\Tables\Actions\DetachBulkAction as TableDetachBulkAction;
use Filament\Tables\Columns\TextColumn;

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
                TextInput::make(Company::BRANCH)
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(Company::CODE)
                    ->label(Str::formatTitle(__('company.code'))),

                TextColumn::make(Company::BRANCH)
                    ->label(Str::formatTitle(__('company.branch'))),

                TextColumn::make(Company::NAME)
                    ->label(Str::formatTitle(__('company.name'))),

                TextColumn::make(CompanyUser::BUYER_CODE)
                    ->label(Str::formatTitle(__('user.buyer_code'))),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                TableAttachAction::make(),
            ])
            ->actions([
                TableDetachAction::make(),
            ])
            ->bulkActions([
                TableDetachBulkAction::make(),
            ]);
    }
}
