<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\CompaniesRelationManager;
use App\Models\User;
use App\Utils\Str;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Hash;
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
                Forms\Components\TextInput::make(User::NAME)
                    ->label(Str::formatTitle(__('user.name')))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make(User::EMAIL)
                    ->label(Str::formatTitle(__('user.email')))
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make(User::PASSWORD)
                    ->label(Str::formatTitle(__('user.password')))
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\Select::make(User::ROLES)
                    ->label(Str::formatTitle(__('user.roles')))
                    ->multiple()
                    ->relationship(User::ROLES, 'name')
                    ->required(),
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
