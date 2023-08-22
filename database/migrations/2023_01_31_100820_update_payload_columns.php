<?php

use App\Enums\PayloadStoringStatusEnum;
use App\Models\Payload;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Payload::TABLE_NAME, static function (Blueprint $table) {
            $table->string(Payload::STORING_STATUS)->default(PayloadStoringStatusEnum::STORED);
            $table->string(Payload::PROCESSING_STATUS)->nullable();
        });

        Payload::query()->each(static function (Payload $payload) {
            $payload->update([
                Payload::STORING_STATUS => $payload->storing_status,
                Payload::PROCESSING_STATUS => $payload->processing_status,
            ]);
        });

        Schema::table(Payload::TABLE_NAME, static function (Blueprint $table) {
            $table->dropColumn('stored_status');
            $table->dropColumn('processed_status');
        });
    }
};
