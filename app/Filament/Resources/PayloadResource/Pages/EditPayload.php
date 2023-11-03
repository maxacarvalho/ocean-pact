<?php

namespace App\Filament\Resources\PayloadResource\Pages;

use App\Filament\Resources\PayloadResource;
use App\Models\IntegraHub\Payload;
use Filament\Actions\DeleteAction as PageDeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPayload extends EditRecord
{
    protected static string $resource = PayloadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PageDeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data[Payload::PAYLOAD] = json_encode($data[Payload::PAYLOAD], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);

        return $data;
    }
}
