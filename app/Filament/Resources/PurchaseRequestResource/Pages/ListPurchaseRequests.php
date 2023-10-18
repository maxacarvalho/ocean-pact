<?php

namespace App\Filament\Resources\PurchaseRequestResource\Pages;

use App\Filament\Resources\PurchaseRequestResource;
use App\Utils\Str;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListPurchaseRequests extends ListRecords
{
    protected static string $resource = PurchaseRequestResource::class;

    protected function getActions(): array
    {
        return [
            //
            ExportAction::make()
                ->label(Str::ucfirst(__('actions.export')))
                ->icon('far-download')
                ->exports([
                    ExcelExport::make()->fromTable()->queue()
                ])
        ];
    }
}
