<?php

use App\Models\Budget;
use App\Models\Quote;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Quote::TABLE_NAME, function (Blueprint $table) {
            $table->unsignedBigInteger(Quote::BUDGET_ID)->index()->after(Quote::COMPANY_ID);

            $table->foreign(Quote::BUDGET_ID)
                ->references(Budget::ID)
                ->on(Budget::TABLE_NAME);
        });
    }

    public function down(): void
    {
        Schema::table(Quote::TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
};
