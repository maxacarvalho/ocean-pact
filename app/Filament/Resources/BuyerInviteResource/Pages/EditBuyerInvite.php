<?php

namespace App\Filament\Resources\BuyerInviteResource\Pages;

use App\Filament\Resources\BuyerInviteResource;
use Filament\Pages\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBuyerInvite extends EditRecord
{
    protected static string $resource = BuyerInviteResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
