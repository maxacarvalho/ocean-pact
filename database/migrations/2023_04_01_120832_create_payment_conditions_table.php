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
        Schema::create(PaymentCondition::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->string(PaymentCondition::COMPANY_CODE_BRANCH, 10)->nullable()->index();
            $table->string(PaymentCondition::CODE, 3)->index();
            $table->string(PaymentCondition::CONDITION, 40);
            $table->string(PaymentCondition::DESCRIPTION, 15);
            $table->timestamps();

            $table->foreign(PaymentCondition::COMPANY_CODE_BRANCH)
                ->references(Company::CODE_BRANCH)
                ->on(Company::TABLE_NAME)
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(PaymentCondition::TABLE_NAME);
    }
};
