<?php

use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Company::TABLE_NAME, function (Blueprint $table) {
            $table->string(Company::CODE_AND_BUSINESS_NAME)
                ->virtualAs("CONCAT(TRIM(`code`), ' - ', TRIM(`business_name`))")
                ->after(Company::CODE_CODE_BRANCH_AND_BRANCH);
        });
    }
};
