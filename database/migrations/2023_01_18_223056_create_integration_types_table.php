<?php

use App\Models\IntegrationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(IntegrationType::TABLE_NAME, static function (Blueprint $table) {
            $table->id();
            $table->string(IntegrationType::DESCRIPTION);
            $table->string(IntegrationType::TYPE, 20);
            $table->string(IntegrationType::HANDLING_TYPE, 50);
            $table->string(IntegrationType::TARGET_URL);
            $table->timestamps();

            $table->index(IntegrationType::CREATED_AT);
            $table->index(IntegrationType::UPDATED_AT);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(IntegrationType::TABLE_NAME);
    }
};
