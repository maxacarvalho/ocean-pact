<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DELETE FROM payload_processing_attempts WHERE payload_id NOT IN (SELECT payloads.id FROM payloads);');

        Schema::table('payload_processing_attempts', function (Blueprint $table) {
            $table->foreign('payload_id')
                ->references('id')
                ->on('payloads')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payload_processing_attempts', function (Blueprint $table) {
            $table->dropForeign(['payload_id']);
        });
    }
};
