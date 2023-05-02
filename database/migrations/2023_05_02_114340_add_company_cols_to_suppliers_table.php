<?php

use App\Models\Company;
use App\Models\Supplier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Supplier::TABLE_NAME, function (Blueprint $table) {
            $table->string(Supplier::COMPANY_CODE, 10)->index()->after(Supplier::ID);
            $table->string(Supplier::COMPANY_CODE_BRANCH, 10)->nullable()->index()->after(Supplier::COMPANY_CODE);

            $table->foreign(Supplier::COMPANY_CODE)
                ->references(Company::CODE)
                ->on(Company::TABLE_NAME)
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table(Supplier::TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
};
