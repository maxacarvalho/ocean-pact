<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('quote_analysis_actions')->truncate();

        Schema::table('quote_analysis_actions', function (Blueprint $table) {
            $table->string('quote_number')->after('quote_id');
        });
    }

    public function down(): void
    {
        Schema::table('quote_analysis_actions', function (Blueprint $table) {
            $table->dropColumn('quote_number');
        });
    }
};
