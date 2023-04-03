<?php

namespace App\Filament\Resources;

use App\Enums\BudgetStatusEnum;
use App\Filament\Resources\BudgetResource\Pages;
use App\Models\Budget;
use App\Models\Company;
use App\Utils\Str;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('budget.budgets'));
    }

    public static function getModelLabel(): string
    {
        return Str::formatTitle(__('budget.budget'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('budget.budgets'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make(Budget::COMPANY_ID)
                    ->label(Str::formatTitle(__('budget.company_id')))
                    ->required()
                    ->relationship(Budget::RELATION_COMPANY, Company::CODE_BRANCH)
                    ->getOptionLabelFromRecordUsing(function (Model|Company $record) {
                        return "$record->code_branch - $record->branch";
                    }),

                TextInput::make(Budget::BUDGET_NUMBER)
                    ->label(Str::formatTitle(__('budget.budget_number')))
                    ->required(),

                Select::make(Budget::STATUS)
                    ->label(Str::formatTitle(__('budget.status')))
                    ->required()
                    ->options(BudgetStatusEnum::toArray()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(Budget::RELATION_COMPANY.'.'.Company::CODE_BRANCH)
                    ->label(Str::formatTitle(__('company.code_branch')))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make(Budget::RELATION_COMPANY.'.'.Company::BRANCH)
                    ->label(Str::formatTitle(__('company.branch')))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make(Budget::BUDGET_NUMBER)
                    ->label(Str::formatTitle(__('budget.budget_number')))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make(Budget::STATUS)
                    ->label(Str::formatTitle(__('budget.status')))
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn (?string $state): ?string => $state !== null ? BudgetStatusEnum::from($state)->label : null),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit' => Pages\EditBudget::route('/{record}/edit'),
        ];
    }

    protected static function getNavigationGroup(): ?string
    {
        return Str::formatTitle(__('navigation.quotes'));
    }
}
