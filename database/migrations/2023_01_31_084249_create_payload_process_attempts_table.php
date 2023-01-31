<?php

use App\Models\PayloadProcessingAttempt;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(PayloadProcessingAttempt::TABLE_NAME, static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(PayloadProcessingAttempt::PAYLOAD_ID)->index();
            $table->string(PayloadProcessingAttempt::STATUS)->index();
            $table->text(PayloadProcessingAttempt::MESSAGE)->nullable();
            $table->timestamps();

            $table->index(PayloadProcessingAttempt::CREATED_AT);
            $table->index(PayloadProcessingAttempt::UPDATED_AT);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(PayloadProcessingAttempt::TABLE_NAME);
    }
};
