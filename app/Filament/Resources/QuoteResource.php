<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages\CreateQuote;
use App\Filament\Resources\QuoteResource\Pages\EditQuote;
use App\Filament\Resources\QuoteResource\Pages\ListQuotes;
use App\Filament\Resources\QuoteResource\RelationManagers\QuoteItemsRelationManager;
use App\Models\Budget;
use App\Models\Company;
use App\Models\PaymentCondition;
use App\Models\Quote;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use App\Utils\Str;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter as TableFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
                    })
                    ->visible(fn () => Auth::user()->hasAnyRole(Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN)),

                Select::make(Quote::SUPPLIER_ID)
                    ->label(Str::formatTitle(__('quote.supplier_id')))
                    ->required()
                    ->relationship(Quote::RELATION_SUPPLIER, Supplier::NAME)
                    ->visible(fn () => Auth::user()->hasAnyRole(Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN)),

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
                    )
                    ->visible(fn () => Auth::user()->hasAnyRole(Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN)),

                Select::make(Quote::BUDGET_ID)
                    ->label(Str::formatTitle(__('quote.budget_id')))
                    ->required()
                    ->relationship(
                        Quote::RELATION_BUDGET,
                        Budget::BUDGET_NUMBER
                    )
                    ->visible(fn () => Auth::user()->hasAnyRole(Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN)),

                TextInput::make(Quote::QUOTE_NUMBER)
                    ->label(Str::formatTitle(__('quote.quote_number')))
                    ->required()
                    ->visible(fn () => Auth::user()->hasAnyRole(Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN)),

                DatePicker::make(Quote::VALID_UNTIL)
                    ->label(Str::formatTitle(__('quote.valid_until')))
                    ->required()
                    ->hiddenOn('create'),

                Textarea::make(Quote::COMMENTS)
                    ->label(Str::formatTitle(__('quote.comments')))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_info')
                    ->label(Str::formatTitle(__('quote.company_id')))
                    ->sortable([Company::CODE_BRANCH, Company::BRANCH])
                    ->formatStateUsing(function (?string $state, Model|Quote|Company $record): ?string {
                        return "{$record->code_branch} {$record->branch}";
                    }),

                TextColumn::make(Quote::RELATION_BUDGET.'.'.Budget::BUDGET_NUMBER)
                    ->label(Str::formatTitle(__('quote.budget_number')))
                    ->sortable()
                    ->searchable(),

                TextColumn::make(Quote::QUOTE_NUMBER)
                    ->label(Str::formatTitle(__('quote.quote_number')))
                    ->sortable()
                    ->searchable(),

                TextColumn::make(Quote::CREATED_AT)
                    ->label(Str::formatTitle(__('quote.created_at')))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make(Quote::UPDATED_AT)
                    ->label(Str::formatTitle(__('quote.updated_at')))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TableFilter::make(Quote::COMPANY_ID)
                    ->label(Str::formatTitle(__('budget.company_id')))
                    ->form([
                        Select::make(Quote::COMPANY_ID)
                            ->label(Str::formatTitle(__('budget.company_id')))
                            ->options(Company::all()->pluck(Company::CODE_BRANCH_AND_BRANCH, Company::ID)),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data[Quote::COMPANY_ID],
                            fn (Builder $query, int $companyId): Builder => $query->where(Quote::COMPANY_ID, '=', $companyId)
                        );
                    }),
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
            QuoteItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuotes::route('/'),
            'create' => CreateQuote::route('/create'),
            'edit' => EditQuote::route('/{record}/edit'),
        ];
    }

    protected static function getNavigationGroup(): ?string
    {
        return Str::formatTitle(__('navigation.quotes'));
    }
}
