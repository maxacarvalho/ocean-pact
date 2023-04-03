<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Models\Company;
use App\Models\Quote;
use Filament\Resources\Pages\CreateRecord;

class CreateQuote extends CreateRecord
{
    protected static string $resource = QuoteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var Company $company */
        $company = Company::query()->findOrFail($data[Quote::COMPANY_ID]);

        $data[Quote::COMPANY_CODE] = $company->code;
        $data[Quote::COMPANY_CODE_BRANCH] = $company->code_branch;

        return $data;
    }
}
