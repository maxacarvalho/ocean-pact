<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('company_branch_code', 10)->nullable()->index();
            $table->string('code', 3)->index();
            $table->string('condition', 40);
            $table->string('description', 15);
            $table->timestamps();

            $table->foreign('company_branch_code')
                ->references('code_branch')
                ->on('companies')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_conditions');
    }
};
