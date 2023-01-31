<?php

use App\Models\Payload;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Payload::TABLE_NAME, static function (Blueprint $table) {
            $table->renameColumn('stored_status', Payload::STORING_STATUS);
            $table->renameColumn('processed_status', Payload::PROCESSING_STATUS);
        });
    }

    public function down(): void
    {
        Schema::table(Payload::TABLE_NAME, static function (Blueprint $table) {
            $table->renameColumn(Payload::STORING_STATUS, 'stored_status');
            $table->renameColumn(Payload::PROCESSING_STATUS, 'processed_status');
        });
    }
};
