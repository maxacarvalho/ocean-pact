<?php

namespace App\Filament\Resources\SupplierResource\RelationManagers;

use App\Models\QuotesPortal\Supplier;
use App\Utils\Str;
use App\Models\User;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\Action;
use App\Filament\Resources\UserResource;

class SellersRelationManager extends RelationManager
{
    protected static string $relationship = Supplier::RELATION_SELLERS;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return Str::title(__('company.sellers'));
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(User::NAME)
                    ->label(Str::formatTitle(__('user.name'))),

                TextColumn::make(User::EMAIL)
                    ->label(Str::formatTitle(__('user.email'))),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('user_edit')
                    ->label(Str::formatTitle(__('user.edit')))
                    ->icon('fas-pen-to-square')
                    ->url(fn($record): string => 
                        UserResource::getUrl('edit', ['record' => $record])
                    )
            ]);
    }
}