<?php

namespace App\Filament\Resources\SupplierInvitationResource\Pages;

use App\Filament\Resources\SupplierInvitationResource;
use Filament\Resources\Pages\EditRecord;

class EditSupplierInvitation extends EditRecord
{
    protected static string $resource = SupplierInvitationResource::class;

    protected function getActions(): array
    {
        return [];
    }
}
