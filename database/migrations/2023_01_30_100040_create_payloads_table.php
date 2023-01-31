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
        Schema::create(Payload::TABLE_NAME, static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(Payload::INTEGRATION_TYPE_ID)->index();
            $table->json(Payload::PAYLOAD);
            $table->timestamp(Payload::STORED_AT)->nullable()->index();
            $table->string('stored_status')->default(PayloadStoringStatusEnum::STORED());
            $table->timestamp(Payload::PROCESSED_AT)->nullable()->index();
            $table->string('processed_status')->nullable();
            $table->timestamps();

            $table->index(Payload::CREATED_AT);
            $table->index(Payload::UPDATED_AT);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Payload::TABLE_NAME);
    }
};
