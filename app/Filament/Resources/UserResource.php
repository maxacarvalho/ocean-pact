<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\CompaniesRelationManager;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Utils\Str;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
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

                Select::make(User::RELATION_COMPANIES)
                    ->label(Str::formatTitle(__('user.companies')))
                    ->multiple()
                    ->relationship(User::RELATION_COMPANIES, Company::BRANCH)
                    ->getOptionLabelFromRecordUsing(function (Model|Company $record): ?string {
                        return "$record->code_branch - $record->branch";
                    })
                    ->required()
                    ->preload()
                    ->visibleOn('create'),

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

                TextInput::make(User::BUYER_CODE)
                    ->label(Str::formatTitle(__('user.buyer_code')))
                    ->required()
                    ->visible(function (Closure $get) {
                        return Role::query()
                            ->whereIn(Role::ID, $get(User::RELATION_ROLES))
                            ->where(Role::NAME, Role::ROLE_BUYER)
                            ->exists();
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(User::NAME)
                    ->label(Str::formatTitle(__('user.name'))),
                Tables\Columns\TextColumn::make(User::EMAIL)
                    ->label(Str::formatTitle(__('user.email'))),
                Tables\Columns\TextColumn::make(User::CREATED_AT)
                    ->label(Str::formatTitle(__('user.created_at')))
                    ->dateTime(),
                Tables\Columns\TextColumn::make(User::UPDATED_AT)
                    ->label(Str::formatTitle(__('user.updated_at')))
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Impersonate::make('impersonate')->label(Str::formatTitle(__('impersonate'))),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CompaniesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
