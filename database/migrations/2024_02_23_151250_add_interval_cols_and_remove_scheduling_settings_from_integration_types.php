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
            $table->integer('interval')->nullable()->after('path_parameters');
            $table->boolean('is_running')->default(false)->after('interval');
            $table->dateTime('last_run_at')->nullable()->after('is_running');
            $table->dropColumn('scheduling_settings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('integration_types', function (Blueprint $table) {
            $table->dropColumn('interval');
            $table->dropColumn('is_running');
            $table->dropColumn('last_run_at');
            $table->json('scheduling_settings')->nullable();
        });
    }
};
