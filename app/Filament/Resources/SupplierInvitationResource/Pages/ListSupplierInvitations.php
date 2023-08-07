<?php

namespace App\Filament\Resources\SupplierInvitationResource\Pages;

use App\Filament\Resources\SupplierInvitationResource;
use Filament\Resources\Pages\ListRecords;

class ListSupplierInvitations extends ListRecords
{
    protected static string $resource = SupplierInvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
