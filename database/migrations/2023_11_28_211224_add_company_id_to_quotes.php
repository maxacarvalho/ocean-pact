<?php

use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Quote;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
        });

        $quotes = Quote::all();

        foreach ($quotes as $quote) {
            /** @var Company $company */
            $company = Company::query()
                ->where(Company::CODE, '=', $quote->company_code)
                ->where(Company::CODE_BRANCH, '=', $quote->company_code_branch)
                ->firstOrFail();

            Quote::query()
                ->where(Quote::ID, '=', $quote->id)
                ->update([
                    Quote::COMPANY_ID => $company->id,
                ]);
        }

        Schema::table('quotes', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable(false)->change();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign(['company_code']);
            $table->dropColumn(['company_code', 'company_code_branch']);
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
