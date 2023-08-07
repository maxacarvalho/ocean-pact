<?php

namespace App\Filament\Resources\SupplierResource\RelationManagers;

use App\Models\Company;
use App\Models\Supplier;
use App\Utils\Str;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction as TableAttachAction;
use Filament\Tables\Actions\DetachAction as TableDetachAction;
use Filament\Tables\Actions\DetachBulkAction as TableDetachBulkAction;
use Filament\Tables\Table;

class CompaniesRelationManager extends RelationManager
{
    protected static string $relationship = Supplier::RELATION_COMPANIES;

    protected static ?string $recordTitleAttribute = Company::CODE_CODE_BRANCH_AND_BRANCH;

    public static function getModelLabel(): ?string
    {
        return Str::formatTitle(__('company.company'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('company.companies'));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('business_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('business_name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                TableAttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->actions([
                TableDetachAction::make(),
            ])
            ->bulkActions([
                TableDetachBulkAction::make(),
            ]);
    }
}
