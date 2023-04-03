<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentConditionResource\Pages;
use App\Models\Company;
use App\Models\PaymentCondition;
use App\Utils\Str;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
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
                Select::make(PaymentCondition::COMPANY_CODE_BRANCH)
                    ->label(Str::formatTitle(__('payment_condition.branch')))
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
                Tables\Columns\TextColumn::make('company')
                    ->label(Str::formatTitle(__('payment_condition.branch')))
                    ->getStateUsing(function (Model|PaymentCondition $record) {
                        return null !== $record->company
                            ? "{$record->company->code_branch} - {$record->company->branch}"
                            : '';
                    }),
                Tables\Columns\TextColumn::make(PaymentCondition::CODE)
                    ->label(Str::formatTitle(__('payment_condition.code'))),
                Tables\Columns\TextColumn::make(PaymentCondition::CONDITION)
                    ->label(Str::formatTitle(__('payment_condition.condition'))),
                Tables\Columns\TextColumn::make(PaymentCondition::DESCRIPTION)
                    ->label(Str::formatTitle(__('payment_condition.description'))),
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
            'index' => Pages\ListPaymentConditions::route('/'),
            'create' => Pages\CreatePaymentCondition::route('/create'),
            'edit' => Pages\EditPaymentCondition::route('/{record}/edit'),
        ];
    }

    protected static function getNavigationGroup(): ?string
    {
        return Str::formatTitle(__('navigation.quotes'));
    }
}
