<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');

            $table->string('company_code', 10)->index()->after('id');
            $table->string('company_code_branch', 10)->nullable()->index()->after('company_code');

            $table->foreign('company_code')
                ->references('code')
                ->on('companies');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            //
        });
    }
};
