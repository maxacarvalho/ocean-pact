<?php

use App\Models\Budget;
use App\Models\Company;
use App\Models\PaymentCondition;
use App\Models\Quote;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Quote::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger(Quote::SUPPLIER_ID);
            $table->unsignedBigInteger(Quote::PAYMENT_CONDITION_ID);
            $table->unsignedBigInteger(Quote::BUYER_ID)->nullable();
            $table->unsignedBigInteger(Quote::BUDGET_ID);
            $table->string(Quote::QUOTE_NUMBER);
            $table->date(Quote::VALID_UNTIL)->nullable();
            $table->text(Quote::COMMENTS)->nullable();
            $table->timestamps();

            $table->foreign('company_id')
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
                ->references(User::ID)
                ->on(User::TABLE_NAME)
                ->nullOnDelete();

            $table->foreign(Quote::BUDGET_ID)
                ->references(Budget::ID)
                ->on(Budget::TABLE_NAME);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Quote::TABLE_NAME);
    }
};
