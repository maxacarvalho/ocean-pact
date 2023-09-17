<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payloads', function (Blueprint $table) {
            $table->text('error')->nullable()->after('processing_status');
        });
    }

    public function down(): void
    {
        Schema::table('payloads', function (Blueprint $table) {
            //
        });
    }
};
