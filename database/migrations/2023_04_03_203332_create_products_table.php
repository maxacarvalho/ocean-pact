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
        Schema::create(Product::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string(Product::CODE);
            $table->string(Product::DESCRIPTION);
            $table->string(Product::MEASUREMENT_UNIT);
            $table->timestamps();

            $table->foreign('company_id')
                ->references(Company::ID)
                ->on(Company::TABLE_NAME)
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Product::TABLE_NAME);
    }
};
