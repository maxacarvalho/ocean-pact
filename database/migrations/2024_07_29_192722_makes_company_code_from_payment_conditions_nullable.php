<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_conditions', function (Blueprint $table) {
            $table->dropForeign(['company_code']);
        });

        Schema::table('payment_conditions', function (Blueprint $table) {
            $table->string('company_code')->nullable()->change();
        });

        Schema::table('payment_conditions', function (Blueprint $table) {
            $table->foreign('company_code')->references('code')->on('companies');
        });
    }

    public function down(): void
    {
        Schema::table('payment_conditions', function (Blueprint $table) {
            $table->dropForeign(['company_code']);
        });

        Schema::table('payment_conditions', function (Blueprint $table) {
            $table->string('company_code')->nullable(false)->change();
        });

        Schema::table('payment_conditions', function (Blueprint $table) {
            $table->foreign('company_code')->references('code')->on('companies');
        });
    }
};
