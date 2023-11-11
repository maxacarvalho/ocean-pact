<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\CompanyUser;
use App\Models\User;
use App\Utils\Str;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction as TableAttachAction;
use Filament\Tables\Actions\DetachAction as TableDetachAction;
use Filament\Tables\Actions\DetachBulkAction as TableDetachBulkAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompaniesRelationManager extends RelationManager
{
    protected static string $relationship = User::RELATION_COMPANIES;

    protected static ?string $recordTitleAttribute = Company::CODE_CODE_BRANCH_AND_BUSINESS_NAME;

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
                TextInput::make(CompanyUser::BUYER_CODE)
                    ->label(Str::formatTitle(__('user.buyer_code')))
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(Company::CODE)
                    ->label(Str::formatTitle(__('company.code'))),

                TextColumn::make(Company::BRANCH)
                    ->label(Str::formatTitle(__('company.branch'))),

                TextColumn::make(Company::BUSINESS_NAME)
                    ->label(Str::formatTitle(__('company.name'))),

                TextColumn::make(CompanyUser::BUYER_CODE)
                    ->label(Str::formatTitle(__('user.buyer_code'))),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                TableAttachAction::make()
                    ->form(fn (TableAttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make(CompanyUser::BUYER_CODE)
                            ->label(Str::formatTitle(__('user.buyer_code')))
                            ->required(),
                    ])
                    ->preloadRecordSelect(),
            ])
            ->actions([
                TableDetachAction::make(),
                TableEditAction::make(),
            ])
            ->bulkActions([
                TableDetachBulkAction::make(),
            ]);
    }
}
