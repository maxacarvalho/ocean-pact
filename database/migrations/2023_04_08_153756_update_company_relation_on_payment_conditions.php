<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_conditions', function (Blueprint $table) {
            $table->dropForeign('payment_conditions_company_branch_code_foreign');

            $table->unsignedBigInteger('company_id')->nullable()->after('id');
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->nullOnDelete();
        });

        DB::statement(
            'UPDATE '.'payment_conditions'.' SET company_id = company_branch_code'
        );

        Schema::table('payment_conditions', function (Blueprint $table) {
            $table->dropColumn('company_branch_code');
        });
    }

    public function down(): void
    {
        Schema::table('payment_conditions', function (Blueprint $table) {
            //
        });
    }
};
