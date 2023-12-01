<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payload_processing_attempts', function (Blueprint $table) {
            $table->json('response')->nullable()->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('payload_processing_attempts', function (Blueprint $table) {
            $table->dropColumn('response');
        });
    }
};
