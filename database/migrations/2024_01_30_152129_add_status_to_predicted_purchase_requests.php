<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('predicted_purchase_requests', function (Blueprint $table) {
            $table->string('status')->default('DRAFT')->index();
        });
    }

    public function down(): void
    {
        Schema::table('predicted_purchase_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
