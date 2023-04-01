<?php

use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Company::TABLE_NAME, static function (Blueprint $table) {
            $table->renameColumn(Company::BRANCH, Company::CODE_BRANCH);
            $table->renameColumn('cnpj', Company::CNPJ_CPF);
            $table->renameColumn('description', Company::BRANCH);
            $table->renameColumn('legal_name', Company::BUSINESS_NAME);
            $table->renameColumn('trade_name', Company::NAME);
        });

        Schema::table(Company::TABLE_NAME, function (Blueprint $table) {
            $table->dropUnique([Company::CODE, Company::BRANCH]);

            $table->string(Company::CODE, 10)->index()->change();

            $table->string(Company::CODE_BRANCH, 10)->index()->change();

            $table->string(Company::CNPJ_CPF, 14)->change();

            $table->string(Company::BRANCH, 100)->change();

            $table->string(Company::BUSINESS_NAME, 100)->change();

            $table->string(Company::NAME, 100)->change();

            $table->string(Company::PHONE_NUMBER, 100)->nullable();
            $table->string(Company::FAX_NUMBER, 100)->nullable();
            $table->string(Company::STATE_INSCRIPTION, 100)->nullable();
            $table->string(Company::INSCM, 100)->nullable();
            $table->string(Company::ADDRESS, 100)->nullable();
            $table->string(Company::COMPLEMENT, 100)->nullable();
            $table->string(Company::NEIGHBORHOOD, 100)->nullable();
            $table->string(Company::CITY, 100)->nullable();
            $table->string(Company::STATE, 2)->nullable();
            $table->string(Company::POSTAL_CODE, 8)->nullable();
            $table->string(Company::CITY_CODE, 100)->nullable();
            $table->string(Company::CNAE, 100)->nullable();

            $table->unique([Company::CODE, Company::CODE_BRANCH]);
        });
    }

    public function down(): void
    {
        Schema::table(Company::TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
};
