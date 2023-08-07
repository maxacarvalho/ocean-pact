<?php

namespace App\Filament\Resources\BuyerInviteResource\Pages;

use App\Filament\Resources\BuyerInviteResource;
use Filament\Resources\Pages\EditRecord;

class EditBuyerInvite extends EditRecord
{
    protected static string $resource = BuyerInviteResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
