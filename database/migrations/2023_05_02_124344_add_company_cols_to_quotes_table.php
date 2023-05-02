<?php

use App\Models\Company;
use App\Models\Quote;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Quote::TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');

            $table->string(Quote::COMPANY_CODE, 10)->index()->after(Quote::ID);
            $table->string(Quote::COMPANY_CODE_BRANCH, 10)->nullable()->index()->after(Quote::COMPANY_CODE);

            $table->foreign(Quote::COMPANY_CODE)
                ->references(Company::CODE)
                ->on(Company::TABLE_NAME);
        });
    }

    public function down(): void
    {
        Schema::table(Quote::TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
};
