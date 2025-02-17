<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use Filament\Actions\DeleteAction as PageDeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBudget extends EditRecord
{
    protected static string $resource = BudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PageDeleteAction::make(),
        ];
    }
}
