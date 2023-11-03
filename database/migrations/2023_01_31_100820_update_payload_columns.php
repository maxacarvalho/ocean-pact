<?php

use App\Models\IntegraHub\Payload;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payloads', static function (Blueprint $table) {
            $table->string('storing_status')->default('STORED');
            $table->string('processing_status')->nullable();
        });

        Payload::query()->each(static function (Payload $payload) {
            $payload->update([
                'storing_status' => $payload->storing_status,
                'processing_status' => $payload->processing_status,
            ]);
        });

        Schema::table('payloads', static function (Blueprint $table) {
            $table->dropColumn('stored_status');
            $table->dropColumn('processed_status');
        });
    }
};
