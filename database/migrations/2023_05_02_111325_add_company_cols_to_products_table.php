<?php

use App\Models\Company;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Product::TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');

            $table->string(Product::COMPANY_CODE)->index()->after(Product::ID);
            $table->string(Product::COMPANY_CODE_BRANCH)->nullable()->index()->after(Product::COMPANY_CODE);

            $table->foreign(Product::COMPANY_CODE)
                ->references(Company::CODE)
                ->on(Company::TABLE_NAME)
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table(Product::TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
};
