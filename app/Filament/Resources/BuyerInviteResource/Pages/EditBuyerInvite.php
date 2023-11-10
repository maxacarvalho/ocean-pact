<?php

namespace App\Filament\Resources\BuyerInviteResource\Pages;

use App\Filament\Resources\UserInviteResource;
use Filament\Resources\Pages\EditRecord;

class EditBuyerInvite extends EditRecord
{
    protected static string $resource = UserInviteResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
