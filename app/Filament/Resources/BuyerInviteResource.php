<?php

namespace App\Filament\Resources;

use App\Enums\InvitationStatusEnum;
use App\Filament\Resources\BuyerInviteResource\Pages;
use App\Models\BuyerInvitation;
use App\Models\User;
use App\Utils\Str;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;

class BuyerInviteResource extends Resource
{
    protected static ?string $model = BuyerInvitation::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('invitation.buyer_invitations'));
    }

    public static function getModelLabel(): string
    {
        return Str::formatTitle(__('invitation.buyer_invitation'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('invitation.buyer_invitations'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make(BuyerInvitation::BUYER_ID)
                    ->label(Str::formatTitle(__('invitation.buyer_id')))
                    ->required()
                    ->relationship(BuyerInvitation::RELATION_BUYER, User::NAME),

                DatePicker::make(BuyerInvitation::REGISTERED_AT)
                    ->label(Str::formatTitle(__('invitation.registered_at'))),

                Select::make('status')
                    ->label(Str::formatTitle(__('invitation.registered_at')))
                    ->required()
                    ->options(fn () => InvitationStatusEnum::toArray())
                    ->default(InvitationStatusEnum::PENDING()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(BuyerInvitation::RELATION_BUYER.'.'.User::NAME)
                    ->label(Str::formatTitle(__('invitation.buyer_id'))),

                TextColumn::make(BuyerInvitation::STATUS)
                    ->label(Str::formatTitle(__('invitation.status')))
                    ->formatStateUsing(fn (?string $state): ?string => $state !== null ? InvitationStatusEnum::from($state)->label : null),

                TextColumn::make(BuyerInvitation::SENT_AT)
                    ->label(Str::formatTitle(__('invitation.sent_at')))
                    ->dateTime(),

                TextColumn::make(BuyerInvitation::REGISTERED_AT)
                    ->label(Str::formatTitle(__('invitation.registered_at')))
                    ->dateTime(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBuyerInvites::route('/'),
            'create' => Pages\CreateBuyerInvite::route('/create'),
            'edit' => Pages\EditBuyerInvite::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
