<?php

use App\Models\IntegrationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(IntegrationType::TABLE_NAME, function (Blueprint $table) {
            $table->string(IntegrationType::TARGET_URL)->nullable()->change();

            $table->boolean(IntegrationType::IS_VISIBLE)->default(true)->after(IntegrationType::TARGET_URL);
            $table->boolean(IntegrationType::IS_ENABLED)->default(true)->after(IntegrationType::IS_VISIBLE);
            $table->boolean(IntegrationType::IS_PROTECTED)->default(false)->after(IntegrationType::IS_ENABLED);
            $table->boolean(IntegrationType::IS_SYNCHRONOUS)->default(false)->after(IntegrationType::IS_PROTECTED);
            $table->string(IntegrationType::ALLOWS_DUPLICATES)->default(false)->after(IntegrationType::IS_SYNCHRONOUS);
            $table->string(IntegrationType::PROCESSOR)->nullable()->after(IntegrationType::IS_SYNCHRONOUS);
        });
    }
};
