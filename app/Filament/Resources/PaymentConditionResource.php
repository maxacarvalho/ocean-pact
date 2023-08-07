<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentConditionResource\Pages\CreatePaymentCondition;
use App\Filament\Resources\PaymentConditionResource\Pages\EditPaymentCondition;
use App\Filament\Resources\PaymentConditionResource\Pages\ListPaymentConditions;
use App\Models\Company;
use App\Models\PaymentCondition;
use App\Utils\Str;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction as TableDeleteBulkAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter as TableFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Query\Builder;

class PaymentConditionResource extends Resource
{
    protected static ?string $model = PaymentCondition::class;

    protected static ?string $navigationIcon = 'far-credit-card';

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('payment_condition.payment_conditions'));
    }

    public static function getModelLabel(): string
    {
        return Str::formatTitle(__('payment_condition.payment_condition'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('payment_condition.payment_conditions'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make(PaymentCondition::COMPANY_CODE)
                    ->label(Str::formatTitle(__('payment_condition.company_code')))
                    ->relationship(PaymentCondition::RELATION_COMPANY, Company::NAME)
                    ->reactive(),

                Select::make(PaymentCondition::COMPANY_CODE_BRANCH)
                    ->label(Str::formatTitle(__('payment_condition.company_code_branch')))
                    ->options(function (\Filament\Forms\Get $get) {
                        $companyCode = $get(PaymentCondition::COMPANY_CODE);

                        if (null === $companyCode) {
                            return [];
                        }

                        return Company::query()
                            ->where(Company::CODE, '=', $companyCode)
                            ->pluck(Company::BRANCH, Company::CODE_BRANCH)
                            ->toArray();
                    }),

                TextInput::make(PaymentCondition::CODE)
                    ->label(Str::formatTitle(__('payment_condition.code')))
                    ->required()
                    ->minLength(1)
                    ->maxLength(3),

                TextInput::make(PaymentCondition::DESCRIPTION)
                    ->label(Str::formatTitle(__('payment_condition.description')))
                    ->required()
                    ->minLength(1)
                    ->maxLength(15),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
                    ->label(Str::formatTitle(__('payment_condition.company_code')))
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy(
                            Company::query()
                                ->select(Company::TABLE_NAME.'.'.Company::BUSINESS_NAME)
                                ->whereColumn(
                                    Company::TABLE_NAME.'.'.Company::CODE,
                                    '=',
                                    PaymentCondition::TABLE_NAME.'.'.PaymentCondition::COMPANY_CODE
                                )
                                ->limit(1),
                            $direction
                        );
                    }),

                TextColumn::make('company_branch')
                    ->label(Str::formatTitle(__('payment_condition.company_code_branch')))
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy(
                            Company::query()
                                ->select(Company::TABLE_NAME.'.'.Company::BRANCH)
                                ->whereColumn(
                                    Company::TABLE_NAME.'.'.Company::CODE,
                                    '=',
                                    PaymentCondition::TABLE_NAME.'.'.PaymentCondition::COMPANY_CODE
                                )
                                ->whereColumn(
                                    Company::TABLE_NAME.'.'.Company::CODE_BRANCH,
                                    '=',
                                    PaymentCondition::TABLE_NAME.'.'.PaymentCondition::COMPANY_CODE_BRANCH
                                )
                                ->limit(1),
                            $direction
                        );
                    }),

                TextColumn::make(PaymentCondition::CODE)
                    ->label(Str::formatTitle(__('payment_condition.code')))
                    ->sortable()
                    ->searchable(),

                TextColumn::make(PaymentCondition::DESCRIPTION)
                    ->label(Str::formatTitle(__('payment_condition.description')))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                TableFilter::make(PaymentCondition::COMPANY_CODE)
                    ->label(Str::formatTitle(__('budget.company_code')))
                    ->form([
                        Select::make(PaymentCondition::COMPANY_CODE)
                            ->label(Str::formatTitle(__('budget.company_code')))
                            ->options(fn () => Company::all()->pluck(Company::CODE_BRANCH_AND_BRANCH, Company::CODE)),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data[PaymentCondition::COMPANY_CODE],
                            fn (Builder $query, string $companyCode): Builder => $query->where(PaymentCondition::COMPANY_CODE, '=', $companyCode)
                        );
                    }),
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
            'index' => ListPaymentConditions::route('/'),
            'create' => CreatePaymentCondition::route('/create'),
            'edit' => EditPaymentCondition::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return Str::formatTitle(__('navigation.quotes'));
    }
}
