<?php

namespace App\Filament\Resources\PayloadResource\Pages;

use App\Filament\Resources\PayloadResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayloads extends ListRecords
{
    protected static string $resource = PayloadResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
