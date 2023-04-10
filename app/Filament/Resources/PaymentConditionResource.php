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
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteBulkAction as TableDeleteBulkAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter as TableFilter;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;

class PaymentConditionResource extends Resource
{
    protected static ?string $model = PaymentCondition::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

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
                Select::make(PaymentCondition::COMPANY_ID)
                    ->label(Str::formatTitle(__('payment_condition.company_id')))
                    ->relationship(PaymentCondition::RELATION_COMPANY, Company::CODE_BRANCH)
                    ->getOptionLabelFromRecordUsing(function (Model|Company $record) {
                        return "$record->code_branch - $record->branch";
                    }),

                TextInput::make(PaymentCondition::CODE)
                    ->label(Str::formatTitle(__('payment_condition.code')))
                    ->required()
                    ->minLength(1)
                    ->maxLength(3),

                TextInput::make(PaymentCondition::CONDITION)
                    ->label(Str::formatTitle(__('payment_condition.condition')))
                    ->required()
                    ->minLength(1)
                    ->maxLength(40),

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
                TextColumn::make('company_info')
                    ->label(Str::formatTitle(__('payment_condition.company')))
                    ->sortable([Company::CODE_BRANCH, Company::BRANCH])
                    ->formatStateUsing(function (?string $state, Model|PaymentCondition|Company $record): ?string {
                        return "{$record->code_branch} {$record->branch}";
                    }),

                TextColumn::make(PaymentCondition::CODE)
                    ->label(Str::formatTitle(__('payment_condition.code')))
                    ->sortable()
                    ->searchable(),

                TextColumn::make(PaymentCondition::CONDITION)
                    ->label(Str::formatTitle(__('payment_condition.condition')))
                    ->sortable()
                    ->searchable(),

                TextColumn::make(PaymentCondition::DESCRIPTION)
                    ->label(Str::formatTitle(__('payment_condition.description')))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                TableFilter::make(PaymentCondition::COMPANY_ID)
                    ->label(Str::formatTitle(__('budget.company_id')))
                    ->form([
                        Select::make(PaymentCondition::COMPANY_ID)
                            ->label(Str::formatTitle(__('budget.company_id')))
                            ->options(Company::all()->pluck(Company::CODE_BRANCH_AND_BRANCH, Company::ID)),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data[PaymentCondition::COMPANY_ID],
                            fn (Builder $query, int $companyId): Builder => $query->where(PaymentCondition::COMPANY_ID, '=', $companyId)
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

    protected static function getNavigationGroup(): ?string
    {
        return Str::formatTitle(__('navigation.quotes'));
    }
}
