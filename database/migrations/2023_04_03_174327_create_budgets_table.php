<?php

use App\Enums\BudgetStatusEnum;
use App\Models\Budget;
use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Budget::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(Budget::COMPANY_ID)->index();
            $table->string(Budget::BUDGET_NUMBER);
            $table->string(Budget::STATUS)->default(BudgetStatusEnum::OPEN())->index();
            $table->timestamps();

            $table->foreign(Budget::COMPANY_ID)
                ->references(Company::ID)
                ->on(Company::TABLE_NAME);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Budget::TABLE_NAME);
    }
};
