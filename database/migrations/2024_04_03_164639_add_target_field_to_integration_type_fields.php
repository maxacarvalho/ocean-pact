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
            $table->foreignId('target_integration_type_field_id')
                ->nullable()
                ->after('field_rules')
                ->constrained('integration_type_fields');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('integration_type_fields', function (Blueprint $table) {
            $table->dropConstrainedForeignId('target_integration_type_field_id');
        });
    }
};
