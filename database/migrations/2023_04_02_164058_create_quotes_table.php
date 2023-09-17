<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('payment_condition_id');
            $table->unsignedBigInteger('buyer_id')->nullable();
            $table->unsignedBigInteger('budget_id');
            $table->string('quote_number');
            $table->date('valid_until')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->nullOnDelete();

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers');

            $table->foreign('payment_condition_id')
                ->references('id')
                ->on('payment_conditions');

            $table->foreign('buyer_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('budget_id')
                ->references('id')
                ->on('budgets');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
