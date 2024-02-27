<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('integration_types', function (Blueprint $table) {
            $table->json('authorization')->nullable()->after('scheduling_settings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('integration_types', function (Blueprint $table) {
            $table->dropColumn('authorization');
        });
    }
};
