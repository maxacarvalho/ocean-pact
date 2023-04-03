<?php

use App\Models\Company;
use App\Models\PaymentCondition;
use App\Models\Quote;
use App\Models\Supplier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Quote::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(Quote::COMPANY_ID)->nullable();
            $table->unsignedBigInteger(Quote::SUPPLIER_ID);
            $table->unsignedBigInteger(Quote::PAYMENT_CONDITION_ID);
            $table->unsignedBigInteger(Quote::BUYER_ID)->nullable();
            $table->string(Quote::COMPANY_CODE, 10)->index();
            $table->string(Quote::COMPANY_CODE_BRANCH, 10)->nullable()->index();
            $table->string(Quote::BUDGET_NUMBER);
            $table->string(Quote::QUOTE_NUMBER);
            $table->text(Quote::COMMENTS)->nullable();
            $table->timestamps();

            $table->foreign(Quote::COMPANY_ID)
                ->references(Company::ID)
                ->on(Company::TABLE_NAME)
                ->nullOnDelete();

            $table->foreign(Quote::SUPPLIER_ID)
                ->references(Supplier::ID)
                ->on(Supplier::TABLE_NAME);

            $table->foreign(Quote::PAYMENT_CONDITION_ID)
                ->references(PaymentCondition::ID)
                ->on(PaymentCondition::TABLE_NAME);

            $table->foreign(Quote::BUYER_ID)
                ->references(Company::ID)
                ->on(Company::TABLE_NAME)
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Quote::TABLE_NAME);
    }
};
