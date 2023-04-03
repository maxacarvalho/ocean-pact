<?php

namespace App\Filament\Resources\PaymentConditionResource\Pages;

use App\Filament\Resources\PaymentConditionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPaymentConditions extends ListRecords
{
    protected static string $resource = PaymentConditionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->with('company');
    }
}
