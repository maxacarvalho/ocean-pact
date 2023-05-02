<?php

use App\Models\Company;
use App\Models\PaymentCondition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(PaymentCondition::TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');

            $table->string(PaymentCondition::COMPANY_CODE, 10)
                ->after(PaymentCondition::ID);
            $table->string(PaymentCondition::COMPANY_CODE_BRANCH, 10)->nullable()
                ->after(PaymentCondition::COMPANY_CODE);

            $table->foreign(PaymentCondition::COMPANY_CODE)
                ->references(Company::CODE)
                ->on(Company::TABLE_NAME)
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table(PaymentCondition::TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
};
