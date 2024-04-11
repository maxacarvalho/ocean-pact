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
        Schema::create('integration_type_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_type_id')->constrained('integration_types');
            $table->foreignId('linked_integration_type_id')->constrained('integration_types');
            $table->timestamps();

            $table->unique(['integration_type_id', 'linked_integration_type_id'], 'integration_type_links_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integration_type_links');
    }
};
