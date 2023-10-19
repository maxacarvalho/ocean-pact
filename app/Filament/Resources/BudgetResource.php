<?php

namespace App\Filament\Resources;

use App\Enums\BudgetStatusEnum;
use App\Filament\Resources\BudgetResource\Pages\CreateBudget;
use App\Filament\Resources\BudgetResource\Pages\EditBudget;
use App\Filament\Resources\BudgetResource\Pages\ListBudgets;
use App\Models\Budget;
use App\Models\Company;
use App\Models\Quote;
use App\Utils\Str;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction as TableDeleteBulkAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter as TableFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Query\Builder as DbQueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static ?string $navigationIcon = 'far-cart-shopping-fast';

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
                    ->options(function (\Filament\Forms\Get $get) {
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
                    ->options(BudgetStatusEnum::class),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (EloquentBuilder $query) {
                return $query
                    ->select([
                        Budget::TABLE_NAME.'.'.Budget::ID,
                        Budget::TABLE_NAME.'.'.Budget::COMPANY_CODE,
                        Budget::TABLE_NAME.'.'.Budget::COMPANY_CODE_BRANCH,
                        Budget::TABLE_NAME.'.'.Budget::BUDGET_NUMBER,
                        Budget::TABLE_NAME.'.'.Budget::STATUS,
                        Budget::TABLE_NAME.'.'.Budget::CREATED_AT,
                        Budget::TABLE_NAME.'.'.Budget::UPDATED_AT,
                    ])
                    ->addSelect([
                        'company_name' => fn (DbQueryBuilder $query) => $query->select(Company::BUSINESS_NAME)
                            ->from(Company::TABLE_NAME)
                            ->whereColumn(
                                Company::TABLE_NAME.'.'.Company::CODE,
                                '=',
                                Budget::TABLE_NAME.'.'.Budget::COMPANY_CODE
                            )
                            ->limit(1),
                        'company_branch' => fn (DbQueryBuilder $query) => $query->select(Company::BRANCH)
                            ->from(Company::TABLE_NAME)
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
                    ]);
            })
            ->columns([
                TextColumn::make('company_name')
                    ->label(Str::formatTitle(__('budget.company_code')))
                    ->sortable(query: function (DbQueryBuilder $query, string $direction): DbQueryBuilder {
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
                    ->sortable(query: function (DbQueryBuilder $query, string $direction): DbQueryBuilder {
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
                    ->sortable(),
            ])
            ->filters([
                TableFilter::make(Budget::COMPANY_CODE)
                    ->label(Str::formatTitle(__('budget.company_code')))
                    ->form([
                        Select::make(Budget::COMPANY_CODE)
                            ->label(Str::formatTitle(__('budget.company_code')))
                            ->options(fn () => Company::all()->pluck(Company::CODE_BRANCH_AND_BRANCH, Company::CODE)),
                    ])
                    ->query(function (DbQueryBuilder $query, array $data): DbQueryBuilder {
                        return $query->when(
                            $data[Budget::COMPANY_CODE],
                            fn (DbQueryBuilder $query, string $companyCode): DbQueryBuilder => $query->where(Budget::COMPANY_CODE, '=', $companyCode)
                        );
                    }),

                SelectFilter::make(Budget::STATUS)
                    ->label(Str::formatTitle(__('budget.status')))
                    ->options(BudgetStatusEnum::class),
            ])
            ->actions([
                TableEditAction::make(),
            ])
            ->bulkActions([
                TableDeleteBulkAction::make(),
                ExportBulkAction::make()->exports([
                    ExcelExport::make()->fromTable()
                        ->withFilename(fn ($resource) => Str::slug($resource::getPluralModelLabel()).'-'.now()->format('Y-m-d'))
                        ->withColumns([
                            Column::make(Quote::STATUS)
                                ->formatStateUsing(fn (BudgetStatusEnum $state) => $state->getLabel()),
                        ]),
                ]),
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

    public static function getNavigationGroup(): ?string
    {
        return Str::formatTitle(__('navigation.quotes'));
    }
}
