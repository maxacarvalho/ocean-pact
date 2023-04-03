<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Models\Company;
use App\Models\Quote;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var Company $company */
        $company = Company::query()->findOrFail($data[Quote::COMPANY_ID]);

        $data[Quote::COMPANY_CODE] = $company->code;
        $data[Quote::COMPANY_CODE_BRANCH] = $company->code_branch;

        return $data;
    }
}
