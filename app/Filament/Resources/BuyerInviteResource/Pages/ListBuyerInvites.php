<?php

namespace App\Filament\Resources\BuyerInviteResource\Pages;

use App\Filament\Resources\BuyerInviteResource;
use Filament\Resources\Pages\ListRecords;

class ListBuyerInvites extends ListRecords
{
    protected static string $resource = BuyerInviteResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
