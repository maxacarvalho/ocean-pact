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
        Schema::table('integration_type_fields', function (Blueprint $table) {
            $table->dropColumn('alternate_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('integration_type_fields', function (Blueprint $table) {
            $table->string('alternate_name')->nullable()->after('field_rules');
        });
    }
};
