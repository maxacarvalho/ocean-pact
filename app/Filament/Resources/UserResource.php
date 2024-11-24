<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\RelationManagers\CompaniesRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\SuppliersRelationManager;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Supplier;
use App\Models\Role;
use App\Models\User;
use App\Utils\Str;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction as TableDeleteBulkAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use STS\FilamentImpersonate\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('user.users'));
    }

    public static function getModelLabel(): string
    {
        return Str::formatTitle(__('user.user'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('user.users'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make(User::NAME)
                    ->label(Str::formatTitle(__('user.name')))
                    ->required()
                    ->maxLength(255),

                TextInput::make(User::EMAIL)
                    ->label(Str::formatTitle(__('user.email')))
                    ->email()
                    ->required()
                    ->maxLength(255),

                Select::make(User::RELATION_ROLES)
                    ->label(Str::formatTitle(__('user.roles')))
                    ->multiple()
                    ->relationship(User::RELATION_ROLES, Role::NAME)
                    ->getOptionLabelFromRecordUsing(function (Model|Role $record): ?string {
                        return Str::formatTitle(__("role.{$record->name}"));
                    })
                    ->required()
                    ->preload()
                    ->reactive(),

                Select::make(User::RELATION_COMPANIES)
                    ->label(Str::formatTitle(__('user.companies')))
                    ->multiple()
                    ->relationship(User::RELATION_COMPANIES, Company::BRANCH)
                    ->getOptionLabelFromRecordUsing(function (Model|Company $record): ?string {
                        return "$record->code_branch - $record->branch";
                    })
                    ->preload()
                    ->visible(function (string $context, \Filament\Forms\Get $get) {
                        if ($context !== 'create') {
                            return false;
                        }

                        return Role::query()
                            ->whereIn(Role::ID, $get(User::RELATION_ROLES))
                            ->whereIn(Role::NAME, [
                                Role::ROLE_SUPER_ADMIN,
                                Role::ROLE_ADMIN,
                                Role::ROLE_USER,
                                Role::ROLE_BUYER,
                            ])
                            ->exists();
                    }),

                Select::make(User::SUPPLIER_ID)
                    ->label(Str::formatTitle(__('user.supplier_id')))
                    ->relationship(User::RELATION_SUPPLIER, Supplier::NAME)
                    ->visible(function (\Filament\Forms\Get $get) {
                        return Role::query()
                            ->whereIn(Role::ID, $get(User::RELATION_ROLES))
                            ->where(Role::NAME, Role::ROLE_SELLER)
                            ->exists();
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(User::NAME)
                    ->label(Str::formatTitle(__('user.name')))
                    ->searchable(),

                TextColumn::make(User::EMAIL)
                    ->label(Str::formatTitle(__('user.email')))
                    ->searchable(),

                IconColumn::make('buyer')
                    ->label(Str::formatTitle(__('user.is_buyer')))
                    ->alignCenter()
                    ->color('success')
                    ->getStateUsing(fn (Model|User $record): bool => $record->isBuyer())
                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : ''),

                IconColumn::make('seller')
                    ->label(Str::formatTitle(__('user.is_seller')))
                    ->alignCenter()
                    ->color('success')
                    ->getStateUsing(fn (Model|User $record): bool => $record->isSeller())
                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : ''),
            ])
            ->filters([
                //
            ])
            ->actions([
                Impersonate::make('impersonate')->label(Str::formatTitle(__('impersonate'))),
                TableEditAction::make(),
            ])
            ->bulkActions([
                TableDeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CompaniesRelationManager::class,
            SuppliersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
