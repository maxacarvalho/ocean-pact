<?php

namespace App\Filament\Resources;

use App\Enums\BudgetStatusEnum;
use App\Filament\Resources\BudgetResource\Pages\CreateBudget;
use App\Filament\Resources\BudgetResource\Pages\EditBudget;
use App\Filament\Resources\BudgetResource\Pages\ListBudgets;
use App\Models\Budget;
use App\Models\Company;
use App\Utils\Str;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteBulkAction as TableDeleteBulkAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter as TableFilter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Database\Query\Builder;

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
                Select::make(Budget::COMPANY_CODE)
                    ->label(Str::formatTitle(__('budget.company_code')))
                    ->relationship(Budget::RELATION_COMPANY, Company::NAME)
                    ->reactive(),

                Select::make(Budget::COMPANY_CODE_BRANCH)
                    ->label(Str::formatTitle(__('budget.company_code_branch')))
                    ->options(function (Closure $get) {
                        $companyCode = $get(Budget::COMPANY_CODE);

                        if (null === $companyCode) {
                            return [];
                        }

                        return Company::query()
                            ->where(Company::CODE, '=', $companyCode)
                            ->pluck(Company::BRANCH, Company::CODE_BRANCH)
                            ->toArray();
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
                TextColumn::make('company_name')
                    ->label(Str::formatTitle(__('budget.company_code')))
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy(
                            Company::query()
                                ->select(Company::TABLE_NAME.'.'.Company::BUSINESS_NAME)
                                ->whereColumn(
                                    Company::TABLE_NAME.'.'.Company::CODE,
                                    '=',
                                    Budget::TABLE_NAME.'.'.Budget::COMPANY_CODE
                                )
                                ->limit(1),
                            $direction
                        );
                    }),

                TextColumn::make('company_branch')
                    ->label(Str::formatTitle(__('budget.company_code_branch')))
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy(
                            Company::query()
                                ->select(Company::TABLE_NAME.'.'.Company::BRANCH)
                                ->whereColumn(
                                    Company::TABLE_NAME.'.'.Company::CODE,
                                    '=',
                                    Budget::TABLE_NAME.'.'.Budget::COMPANY_CODE
                                )
                                ->whereColumn(
                                    Company::TABLE_NAME.'.'.Company::CODE_BRANCH,
                                    '=',
                                    Budget::TABLE_NAME.'.'.Budget::COMPANY_CODE_BRANCH
                                )
                                ->limit(1),
                            $direction
                        );
                    }),

                TextColumn::make(Budget::BUDGET_NUMBER)
                    ->label(Str::formatTitle(__('budget.budget_number')))
                    ->sortable()
                    ->searchable(),

                TextColumn::make(Budget::STATUS)
                    ->label(Str::formatTitle(__('budget.status')))
                    ->sortable()
                    ->formatStateUsing(fn (?string $state): ?string => $state !== null ? BudgetStatusEnum::from($state)->label : null),
            ])
            ->filters([
                TableFilter::make(Budget::COMPANY_CODE)
                    ->label(Str::formatTitle(__('budget.company_code')))
                    ->form([
                        Select::make(Budget::COMPANY_CODE)
                            ->label(Str::formatTitle(__('budget.company_code')))
                            ->options(fn () => Company::all()->pluck(Company::CODE_BRANCH_AND_BRANCH, Company::CODE)),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data[Budget::COMPANY_CODE],
                            fn (Builder $query, string $companyCode): Builder => $query->where(Budget::COMPANY_CODE, '=', $companyCode)
                        );
                    }),

                SelectFilter::make(Budget::STATUS)
                    ->label(Str::formatTitle(__('budget.status')))
                    ->options(fn () => BudgetStatusEnum::toArray()),
            ])
            ->actions([
                TableEditAction::make(),
            ])
            ->bulkActions([
                TableDeleteBulkAction::make(),
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
            'index' => ListBudgets::route('/'),
            'create' => CreateBudget::route('/create'),
            'edit' => EditBudget::route('/{record}/edit'),
        ];
    }

    protected static function getNavigationGroup(): ?string
    {
        return Str::formatTitle(__('navigation.quotes'));
    }
}
