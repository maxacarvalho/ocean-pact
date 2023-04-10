<?php

use App\Models\Company;
use App\Models\PaymentCondition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(PaymentCondition::TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign('payment_conditions_company_branch_code_foreign');

            $table->unsignedBigInteger(PaymentCondition::COMPANY_ID)->nullable()->after(PaymentCondition::ID);
            $table->foreign(PaymentCondition::COMPANY_ID)
                ->references(Company::ID)
                ->on(Company::TABLE_NAME)
                ->nullOnDelete();
        });

        DB::statement(
            'UPDATE '.PaymentCondition::TABLE_NAME.' SET '.PaymentCondition::COMPANY_ID.' = company_branch_code'
        );

        Schema::table(PaymentCondition::TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn('company_branch_code');
        });
    }

    public function down(): void
    {
        Schema::table(PaymentCondition::TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
};
