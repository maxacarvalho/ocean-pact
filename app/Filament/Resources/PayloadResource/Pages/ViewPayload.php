<?php

namespace App\Filament\Resources\PayloadResource\Pages;

use App\Filament\Resources\PayloadResource;
use App\Models\Payload;
use Filament\Resources\Pages\ViewRecord;

class ViewPayload extends ViewRecord
{
    protected static string $resource = PayloadResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data[Payload::PAYLOAD] = json_encode($data[Payload::PAYLOAD], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);

        return $data;
    }
}
