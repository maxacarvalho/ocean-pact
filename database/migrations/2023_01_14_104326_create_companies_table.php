<?php

use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Company::TABLE_NAME, static function (Blueprint $table) {
            $table->id();
            $table->string(Company::CODE, 10);
            $table->string(Company::BRANCH, 10);
            $table->string(Company::CNPJ, 18)->unique();
            $table->string(Company::DESCRIPTION, 255);
            $table->string(Company::LEGAL_NAME, 255);
            $table->string(Company::TRADE_NAME, 255);
            $table->timestamps();
            $table->softDeletes();

            $table->unique([Company::CODE, Company::BRANCH]);
            $table->index(Company::CREATED_AT);
            $table->index(Company::UPDATED_AT);
            $table->index(Company::DELETED_AT);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
