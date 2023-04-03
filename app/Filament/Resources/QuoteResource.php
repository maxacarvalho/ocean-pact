<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Models\Company;
use App\Models\PaymentCondition;
use App\Models\Quote;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use App\Utils\Str;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('quote.quotes'));
    }

    public static function getModelLabel(): string
    {
        return Str::formatTitle(__('quote.quote'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('quote.quotes'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make(Quote::COMPANY_ID)
                    ->label(Str::formatTitle(__('quote.company_id')))
                    ->required()
                    ->relationship(Quote::RELATION_COMPANY, Company::CODE_BRANCH)
                    ->getOptionLabelFromRecordUsing(function (Model|Company $record) {
                        return "$record->code_branch - $record->branch";
                    }),

                Select::make(Quote::SUPPLIER_ID)
                    ->label(Str::formatTitle(__('quote.supplier_id')))
                    ->required()
                    ->relationship(Quote::RELATION_SUPPLIER, Supplier::NAME),

                Select::make(Quote::PAYMENT_CONDITION_ID)
                    ->label(Str::formatTitle(__('quote.payment_condition_id')))
                    ->required()
                    ->relationship(
                        Quote::RELATION_PAYMENT_CONDITION,
                        PaymentCondition::DESCRIPTION
                    ),

                Select::make(Quote::BUYER_ID)
                    ->label(Str::formatTitle(__('quote.buyer_id')))
                    ->required()
                    ->relationship(
                        Quote::RELATION_BUYER,
                        User::NAME,
                        function (Builder $query) {
                            $query->whereHas(User::RELATION_ROLES, function (Builder $query) {
                                $query->where(Role::NAME, Role::ROLE_BUYER);
                            });
                        }
                    ),

                Forms\Components\TextInput::make(Quote::BUDGET_NUMBER)
                    ->label(Str::formatTitle(__('quote.budget_number')))
                    ->required(),

                Forms\Components\TextInput::make(Quote::QUOTE_NUMBER)
                    ->label(Str::formatTitle(__('quote.quote_number')))
                    ->required(),

                Forms\Components\Textarea::make(Quote::COMMENTS)
                    ->label(Str::formatTitle(__('quote.comments')))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(Quote::COMPANY_CODE)
                    ->label(Str::formatTitle(__('quote.company_code'))),
                Tables\Columns\TextColumn::make(Quote::COMPANY_CODE_BRANCH)
                    ->label(Str::formatTitle(__('quote.company_code_branch'))),
                Tables\Columns\TextColumn::make(Quote::BUDGET_NUMBER)
                    ->label(Str::formatTitle(__('quote.budget_number'))),
                Tables\Columns\TextColumn::make(Quote::QUOTE_NUMBER)
                    ->label(Str::formatTitle(__('quote.quote_number'))),
                Tables\Columns\TextColumn::make(Quote::CREATED_AT)
                    ->label(Str::formatTitle(__('quote.created_at')))
                    ->dateTime(),
                Tables\Columns\TextColumn::make(Quote::UPDATED_AT)
                    ->label(Str::formatTitle(__('quote.updated_at')))
                    ->dateTime(),
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
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }

    protected static function getNavigationGroup(): ?string
    {
        return Str::formatTitle(__('navigation.quotes'));
    }
}
