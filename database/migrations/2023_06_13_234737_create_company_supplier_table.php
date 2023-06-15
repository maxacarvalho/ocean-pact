<?php

use App\Models\Company;
use App\Models\CompanySupplier;
use App\Models\Supplier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(CompanySupplier::TABLE_NAME, function (Blueprint $table) {
            $table->unsignedBigInteger(CompanySupplier::COMPANY_ID);
            $table->unsignedBigInteger(CompanySupplier::SUPPLIER_ID);

            $table->foreign(CompanySupplier::COMPANY_ID)
                ->references(Company::ID)
                ->on(Company::TABLE_NAME)
                ->cascadeOnDelete();

            $table->foreign(CompanySupplier::SUPPLIER_ID)
                ->references(Supplier::ID)
                ->on(Supplier::TABLE_NAME)
                ->cascadeOnDelete();
        });
    }
};
