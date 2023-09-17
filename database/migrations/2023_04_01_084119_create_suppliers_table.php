<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('store', 2)->index();
            $table->string('code', 6)->index();
            $table->string('name', 40);
            $table->string('business_name', 20);
            $table->string('address', 40)->nullable(); // não exibir
            $table->string('number', 6)->nullable(); // não exibir
            $table->string('state_code', 2)->nullable(); // não exibir
            $table->string('postal_code', 8)->nullable(); // não exibir
            $table->string('cnpj_cpf', 14)->nullable(); // não exibir
            $table->string('phone_code', 3)->nullable(); // não exibir
            $table->string('phone_number', 50)->nullable(); // não exibir
            $table->string('contact'); // nome do contato
            $table->string('email'); // permitir N emails, separados por ponto-e-vírgula
            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
