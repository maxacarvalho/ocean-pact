<?php

namespace App\Filament\Resources\BuyerInviteResource\Pages;

use App\Filament\Resources\UserInviteResource;
use Filament\Resources\Pages\ListRecords;

class ListUserInvites extends ListRecords
{
    protected static string $resource = UserInviteResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
