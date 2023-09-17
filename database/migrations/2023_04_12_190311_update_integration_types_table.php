<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('integration_types', function (Blueprint $table) {
            $table->string('target_url')->nullable()->change();

            $table->boolean('is_visible')->default(true)->after('target_url');
            $table->boolean('is_enabled')->default(true)->after('is_visible');
            $table->boolean('is_protected')->default(false)->after('is_enabled');
            $table->boolean('is_synchronous')->default(false)->after('is_protected');
            $table->string('allows_duplicates')->default(false)->after('is_synchronous');
            $table->string('processor')->nullable()->after('is_synchronous');
        });
    }
};
