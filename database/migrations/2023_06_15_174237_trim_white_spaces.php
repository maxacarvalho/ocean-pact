<?php

use App\Models\QuotesPortal\Company;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    public function up(): void
    {
        Company::query()->each(function (Company $company) {
            $company->branch = trim($company->branch);
            $company->name = trim($company->name);
            $company->business_name = trim($company->business_name);
            $company->phone_number = trim($company->phone_number);
            $company->fax_number = trim($company->fax_number);
            $company->cnpj_cpf = trim($company->cnpj_cpf);
            $company->state_inscription = trim($company->state_inscription);
            $company->inscm = trim($company->inscm);
            $company->address = trim($company->address);
            $company->complement = trim($company->complement);
            $company->neighborhood = trim($company->neighborhood);
            $company->city = trim($company->city);
            $company->state = trim($company->state);
            $company->postal_code = trim($company->postal_code);
            $company->city_code = trim($company->city_code);
            $company->cnae = trim($company->cnae);
            $company->save();
        });
    }
};
