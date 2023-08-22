<?php

namespace App\Filament\Resources\PaymentConditionResource\Pages;

use App\Filament\Resources\PaymentConditionResource;
use Filament\Pages\Actions\DeleteAction as PageDeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPaymentCondition extends EditRecord
{
    protected static string $resource = PaymentConditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PageDeleteAction::make(),
        ];
    }
}
