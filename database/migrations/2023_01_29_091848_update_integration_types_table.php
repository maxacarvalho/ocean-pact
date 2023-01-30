<?php

use App\Models\IntegrationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(IntegrationType::TABLE_NAME, static function (Blueprint $table) {
            $table->unsignedBigInteger(IntegrationType::COMPANY_ID)
                ->nullable()
                ->index()
                ->after(IntegrationType::ID);
            $table->string(IntegrationType::CODE)
                ->index()
                ->after(IntegrationType::COMPANY_ID);
        });
    }

    public function down(): void
    {
        Schema::table(IntegrationType::TABLE_NAME, static function (Blueprint $table) {
            $table->dropColumn(IntegrationType::COMPANY_ID);
            $table->dropColumn(IntegrationType::CODE);
        });
    }
};
