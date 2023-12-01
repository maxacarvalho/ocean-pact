<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('integration_types', function (Blueprint $table) {
            $table->json('path_parameters')->nullable()->after('headers');
        });
    }

    public function down(): void
    {
        Schema::table('integration_types', function (Blueprint $table) {
            $table->dropColumn('path_parameters');
        });
    }
};
