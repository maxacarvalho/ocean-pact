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
            $table->string('forward_url')->nullable()->after('authorization');
            $table->json('forward_headers')->nullable()->after('forward_url');
            $table->json('forward_authorization')->nullable()->after('forward_headers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('integration_types', function (Blueprint $table) {
            $table->dropColumn('forward_url');
            $table->dropColumn('forward_headers');
            $table->dropColumn('forward_authorization');
        });
    }
};
