<?php

namespace App\Filament\Resources;

use App\Enums\QuotesPortal\InvitationStatusEnum;
use App\Filament\Resources\BuyerInviteResource\Pages;
use App\Models\QuotesPortal\UserInvitation;
use App\Models\User;
use App\Utils\Str;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserInviteResource extends Resource
{
    protected static ?string $model = UserInvitation::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('invitation.user_invitations'));
    }

    public static function getModelLabel(): string
    {
        return Str::formatTitle(__('invitation.user_invitation'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('invitation.user_invitations'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make(UserInvitation::USER_ID)
                    ->label(Str::formatTitle(__('invitation.buyer_id')))
                    ->required()
                    ->relationship(UserInvitation::RELATION_USER, User::NAME),

                DatePicker::make(UserInvitation::REGISTERED_AT)
                    ->label(Str::formatTitle(__('invitation.registered_at'))),

                Select::make('status')
                    ->label(Str::formatTitle(__('invitation.registered_at')))
                    ->required()
                    ->options(InvitationStatusEnum::class)
                    ->default(InvitationStatusEnum::PENDING),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(UserInvitation::RELATION_USER.'.'.User::NAME)
                    ->label(Str::formatTitle(__('invitation.buyer_id'))),

                TextColumn::make(UserInvitation::STATUS)
                    ->label(Str::formatTitle(__('invitation.status'))),

                TextColumn::make(UserInvitation::SENT_AT)
                    ->label(Str::formatTitle(__('invitation.sent_at')))
                    ->dateTime('d/m/Y H:i:s'),

                TextColumn::make(UserInvitation::REGISTERED_AT)
                    ->label(Str::formatTitle(__('invitation.registered_at')))
                    ->dateTime('d/m/Y H:i:s'),
            ])
            ->actions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserInvites::route('/'),
            'create' => UserInviteResource\Pages\CreateUserInvite::route('/create'),
        ];
    }
}
