<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('integration_types', static function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')
                ->nullable()
                ->index()
                ->after('id');
            $table->string('code')
                ->index()
                ->after('company_id');
        });
    }

    public function down(): void
    {
        Schema::table('integration_types', static function (Blueprint $table) {
            $table->dropColumn('company_id');
            $table->dropColumn('code');
        });
    }
};
