<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\QuotesPortal\SupplierUser;
use App\Utils\Str;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SuppliersRelationManager extends RelationManager
{
    protected static string $relationship = 'suppliers';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return Str::title(__('supplier.suppliers'));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make(SupplierUser::CODE)
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make(SupplierUser::CODE)
                            ->required()
                            ->maxLength(255),
                    ])
                    ->preloadRecordSelect(),
            ])
            ->actions([
                DetachAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
