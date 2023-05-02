<?php

use App\Models\Budget;
use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Budget::TABLE_NAME, function (Blueprint $table) {
            $table->string(Budget::COMPANY_CODE, 10)->index()->after(Budget::ID);
            $table->string(Budget::COMPANY_CODE_BRANCH, 10)->nullable()->index()->after(Budget::COMPANY_CODE);

            $table->foreign(Budget::COMPANY_CODE)
                ->references(Company::CODE)
                ->on(Company::TABLE_NAME)
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table(Budget::TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
};
