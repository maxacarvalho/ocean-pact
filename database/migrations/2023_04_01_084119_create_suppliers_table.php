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
        Schema::create(Supplier::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->string(Supplier::COMPANY_CODE_BRANCH, 10)->nullable()->index();
            $table->string(Supplier::STORE, 2)->index();
            $table->string(Supplier::CODE, 6)->index();
            $table->string(Supplier::NAME, 40);
            $table->string(Supplier::BUSINESS_NAME, 20);
            $table->string(Supplier::ADDRESS, 40)->nullable(); // não exibir
            $table->string(Supplier::NUMBER, 6)->nullable(); // não exibir
            $table->string(Supplier::STATE_CODE, 2);
            $table->string(Supplier::POSTAL_CODE, 8)->nullable(); // não exibir
            $table->string(Supplier::CNPJ_CPF, 14)->nullable(); // não exibir
            $table->string(Supplier::PHONE_CODE, 3)->nullable(); // não exibir
            $table->string(Supplier::PHONE_NUMBER, 50)->nullable(); // não exibir
            $table->string(Supplier::CONTACT, 15); // nome do contato
            $table->string(Supplier::EMAIL, 30); // permitir N emails, separados por ponto-e-vírgula
            $table->timestamps();

            $table->foreign(Supplier::COMPANY_CODE_BRANCH)
                ->references(Company::CODE_BRANCH)
                ->on(Company::TABLE_NAME)
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Supplier::TABLE_NAME);
    }
};
