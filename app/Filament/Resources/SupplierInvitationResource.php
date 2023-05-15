<?php

namespace App\Filament\Resources;

use App\Enums\InvitationStatusEnum;
use App\Filament\Resources\SupplierInvitationResource\Pages;
use App\Models\Supplier;
use App\Models\SupplierInvitation;
use App\Utils\Str;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;

class SupplierInvitationResource extends Resource
{
    protected static ?string $model = SupplierInvitation::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('invitation.supplier_invitations'));
    }

    public static function getModelLabel(): string
    {
        return Str::formatTitle(__('invitation.supplier_invitation'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('invitation.supplier_invitations'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make(SupplierInvitation::SUPPLIER_ID)
                    ->label(Str::formatTitle(__('invitation.supplier_id')))
                    ->required()
                    ->relationship(SupplierInvitation::RELATION_SUPPLIER, Supplier::NAME),

                DatePicker::make(SupplierInvitation::SENT_AT)
                    ->label(Str::formatTitle(__('invitation.sent_at'))),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(SupplierInvitation::RELATION_SUPPLIER.'.'.Supplier::NAME)
                    ->label(Str::formatTitle(__('invitation.supplier_id'))),

                TextColumn::make(SupplierInvitation::SENT_AT)
                    ->label(Str::formatTitle(__('invitation.sent_at')))
                    ->dateTime('d/m/Y H:i:s'),

                TextColumn::make(SupplierInvitation::STATUS)
                    ->label(Str::formatTitle(__('invitation.status')))
                    ->formatStateUsing(fn (?string $state): ?string => $state !== null ? InvitationStatusEnum::from($state)->label : null),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupplierInvitations::route('/'),
            'create' => Pages\CreateSupplierInvitation::route('/create'),
            // 'edit' => Pages\EditSupplierInvitation::route('/{record}/edit'),
        ];
    }
}
