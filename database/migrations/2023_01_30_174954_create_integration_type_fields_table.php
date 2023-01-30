<?php

use App\Models\IntegrationTypeField;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(IntegrationTypeField::TABLE_NAME, static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(IntegrationTypeField::INTEGRATION_TYPE_ID)->index();
            $table->string(IntegrationTypeField::FIELD_NAME);
            $table->string(IntegrationTypeField::FIELD_TYPE);
            $table->json(IntegrationTypeField::FIELD_RULES)->nullable();
            $table->timestamps();

            $table->index(IntegrationTypeField::CREATED_AT);
            $table->index(IntegrationTypeField::UPDATED_AT);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(IntegrationTypeField::TABLE_NAME);
    }
};
